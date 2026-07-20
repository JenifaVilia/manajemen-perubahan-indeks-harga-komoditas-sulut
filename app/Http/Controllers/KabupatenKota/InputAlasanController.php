<?php

namespace App\Http\Controllers\KabupatenKota;

use App\Http\Controllers\Controller;
use App\Models\AlasanPerubahan;
use App\Models\DataHarga;
use App\Models\Komoditas;
use App\Models\Notifikasi;
use App\Models\Periode;
use Illuminate\Http\Request;

class InputAlasanController extends Controller
{
    public function index(Request $request)
    {
        $user    = auth()->user();
        $wilayah = $user->wilayah;

        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodes     = Periode::orderByDesc('tahun')->orderByDesc('bulan')->limit(12)->get();

        $periodeId  = $request->get('periode_id', $periodeAktif?->id);
        $tipeIndeks = $request->get('tipe_indeks', 'IHK');
        $filter     = $request->get('filter', 'perlu'); // perlu | semua | sudah | revisi

        // Ambil data harga wilayah ini untuk periode terpilih
        $query = DataHarga::with(['komoditas'])
            ->where('periode_id', $periodeId)
            ->where('wilayah_id', $wilayah->id)
            ->where('tipe_indeks', $tipeIndeks)
            ->orderByRaw('ABS(inflasi_mtm) DESC');

        $dataHargas = $query->get();

        // Annotate dengan status alasan
        $dataHargas = $dataHargas->map(function($dh) use ($wilayah, $periodeId) {
            $alasan = AlasanPerubahan::where('periode_id', $periodeId)
                ->where('wilayah_id', $wilayah->id)
                ->where('komoditas_id', $dh->komoditas_id)
                ->first();
            $dh->alasan_record = $alasan;
            $dh->status_alasan = $alasan?->status ?? 'belum';
            return $dh;
        });

        // Filter
        $dataHargas = match($filter) {
            'perlu'  => $dataHargas->filter(fn($d) => $d->isSignifikan() && !in_array($d->status_alasan, ['submitted','disetujui'])),
            'sudah'  => $dataHargas->filter(fn($d) => in_array($d->status_alasan, ['submitted','disetujui'])),
            'revisi' => $dataHargas->filter(fn($d) => $d->status_alasan === 'revisi'),
            default  => $dataHargas,
        };

        $perluInputCount = $dataHargas->filter(fn($d) => $d->isSignifikan() && !in_array($d->status_alasan, ['submitted','disetujui']))->count();

        $faktors = AlasanPerubahan::$faktors;

        return view('wilayah.input-alasan.index', compact(
            'dataHargas', 'periodeAktif', 'periodes', 'wilayah',
            'periodeId', 'tipeIndeks', 'filter', 'perluInputCount', 'faktors'
        ));
    }

    public function show(AlasanPerubahan $alasan)
    {
        abort_if($alasan->wilayah_id !== auth()->user()->wilayah_id, 403);
        $alasan->load(['komoditas', 'wilayah', 'periode', 'reviewer']);

        $dataHarga = DataHarga::where('periode_id', $alasan->periode_id)
            ->where('wilayah_id', $alasan->wilayah_id)
            ->where('komoditas_id', $alasan->komoditas_id)
            ->first();

        $faktors = AlasanPerubahan::$faktors;

        return view('wilayah.input-alasan.show', compact('alasan', 'dataHarga', 'faktors'));
    }

    public function store(Request $request)
    {
        $user    = auth()->user();
        $wilayah = $user->wilayah;

        $data = $request->validate([
            'periode_id'      => ['required', 'exists:periodes,id'],
            'komoditas_id'    => ['required', 'exists:komoditas,id'],
            'alasan'          => ['required', 'string', 'min:20', 'max:2000'],
            'faktor_pendorong'=> ['nullable', 'array'],
            'faktor_pendorong.*' => ['string'],
            'rekomendasi'     => ['nullable', 'string', 'max:1000'],
        ], [
            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.min'      => 'Alasan minimal 20 karakter.',
        ]);

        // Cek apakah sudah ada
        $existing = AlasanPerubahan::where('periode_id', $data['periode_id'])
            ->where('wilayah_id', $wilayah->id)
            ->where('komoditas_id', $data['komoditas_id'])
            ->first();

        if ($existing) {
            return back()->withErrors(['komoditas_id' => 'Alasan untuk komoditas ini sudah ada. Gunakan tombol Edit.'])->withInput();
        }

        AlasanPerubahan::create([
            ...$data,
            'wilayah_id'   => $wilayah->id,
            'status'       => 'draft',
            'submitted_by' => $user->id,
        ]);

        return redirect()->route('wilayah.input-alasan.index')->with('success', 'Alasan berhasil disimpan sebagai draft.');
    }

    public function update(Request $request, AlasanPerubahan $alasan)
    {
        abort_if($alasan->wilayah_id !== auth()->user()->wilayah_id, 403);
        abort_if($alasan->status === 'disetujui', 422, 'Alasan yang sudah disetujui tidak dapat diubah.');

        $data = $request->validate([
            'alasan'          => ['required', 'string', 'min:20', 'max:2000'],
            'faktor_pendorong'=> ['nullable', 'array'],
            'faktor_pendorong.*' => ['string'],
            'rekomendasi'     => ['nullable', 'string', 'max:1000'],
        ]);

        $alasan->update([...$data, 'status' => 'draft']);

        return redirect()->route('wilayah.input-alasan.show', $alasan)->with('success', 'Alasan berhasil diperbarui.');
    }

    public function submit(Request $request, AlasanPerubahan $alasan)
    {
        abort_if($alasan->wilayah_id !== auth()->user()->wilayah_id, 403);
        abort_if(in_array($alasan->status, ['disetujui', 'submitted']), 422, 'Alasan sudah dikirimkan.');

        $alasan->update([
            'status'       => 'submitted',
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
        ]);

        return redirect()->route('wilayah.input-alasan.index')->with('success', 'Alasan berhasil dikirimkan ke Provinsi.');
    }
}

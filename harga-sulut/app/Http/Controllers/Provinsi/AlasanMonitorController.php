<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\AlasanPerubahan;
use App\Models\DataHarga;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class AlasanMonitorController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodes     = Periode::orderByDesc('tahun')->orderByDesc('bulan')->limit(24)->get();
        $wilayahs     = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();

        // Filter
        $periodeId = $request->get('periode_id', $periodeAktif?->id);
        $wilayahId = $request->get('wilayah_id');
        $status    = $request->get('status');
        $search    = $request->get('search');

        $query = AlasanPerubahan::with(['komoditas', 'wilayah', 'submitter', 'reviewer', 'periode'])
            ->when($periodeId,  fn($q) => $q->where('periode_id', $periodeId))
            ->when($wilayahId,  fn($q) => $q->where('wilayah_id', $wilayahId))
            ->when($status,     fn($q) => $q->where('status', $status))
            ->when($search,     fn($q) => $q->whereHas('komoditas', fn($qq) => $qq->where('nama_komoditas', 'like', "%{$search}%")))
            ->latest('submitted_at');

        $alasans = $query->paginate(25)->withQueryString();

        // Summary counts untuk filter ini
        $summary = [
            'total'     => $query->count(),
            'submitted' => AlasanPerubahan::when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->where('status', 'submitted')->count(),
            'disetujui' => AlasanPerubahan::when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->where('status', 'disetujui')->count(),
            'revisi'    => AlasanPerubahan::when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->where('status', 'revisi')->count(),
        ];

        return view('provinsi.alasan.index', compact(
            'alasans', 'periodes', 'wilayahs', 'periodeAktif',
            'periodeId', 'wilayahId', 'status', 'search', 'summary'
        ));
    }

    public function show(AlasanPerubahan $alasan)
    {
        $alasan->load(['komoditas', 'wilayah', 'submitter', 'reviewer', 'periode']);

        // Data harga terkait untuk konteks
        $dataHarga = DataHarga::where('periode_id', $alasan->periode_id)
            ->where('wilayah_id', $alasan->wilayah_id)
            ->where('komoditas_id', $alasan->komoditas_id)
            ->first();

        // Histori alasan sebelumnya (3 periode terakhir)
        $histori = AlasanPerubahan::with('periode')
            ->where('wilayah_id', $alasan->wilayah_id)
            ->where('komoditas_id', $alasan->komoditas_id)
            ->where('id', '!=', $alasan->id)
            ->whereIn('status', ['submitted', 'disetujui'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('provinsi.alasan.show', compact('alasan', 'dataHarga', 'histori'));
    }

    public function setujui(AlasanPerubahan $alasan)
    {
        abort_if($alasan->status === 'disetujui', 422, 'Alasan sudah disetujui.');

        $alasan->update([
            'status'      => 'disetujui',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'catatan_provinsi' => null,
        ]);

        // Kirim notifikasi ke user wilayah
        $userWilayah = User::where('wilayah_id', $alasan->wilayah_id)->pluck('id')->toArray();
        if (!empty($userWilayah)) {
            Notifikasi::kirim(
                $userWilayah,
                'Alasan Perubahan Harga Disetujui',
                "Alasan perubahan harga komoditas {$alasan->komoditas->nama_komoditas} periode {$alasan->periode->nama} telah disetujui.",
                'alasan_disetujui',
                $alasan->id,
                AlasanPerubahan::class,
                route('wilayah.input-alasan.show', $alasan->id)
            );
        }

        return redirect()
            ->back()
            ->with('success', "Alasan untuk {$alasan->komoditas->nama_komoditas} telah disetujui.");
    }

    public function mintaRevisi(Request $request, AlasanPerubahan $alasan)
    {
        $request->validate([
            'catatan_provinsi' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'catatan_provinsi.required' => 'Catatan revisi wajib diisi.',
            'catatan_provinsi.min'      => 'Catatan revisi minimal 10 karakter.',
        ]);

        $alasan->update([
            'status'           => 'revisi',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'catatan_provinsi' => $request->catatan_provinsi,
        ]);

        // Notifikasi ke wilayah
        $userWilayah = User::where('wilayah_id', $alasan->wilayah_id)->pluck('id')->toArray();
        if (!empty($userWilayah)) {
            Notifikasi::kirim(
                $userWilayah,
                'Alasan Perlu Direvisi',
                "Alasan perubahan harga komoditas {$alasan->komoditas->nama_komoditas} periode {$alasan->periode->nama} diminta untuk direvisi. Catatan: {$request->catatan_provinsi}",
                'revisi_alasan',
                $alasan->id,
                AlasanPerubahan::class,
                route('wilayah.input-alasan.show', $alasan->id)
            );
        }

        return redirect()
            ->back()
            ->with('success', "Permintaan revisi dikirimkan ke {$alasan->wilayah->nama_wilayah}.");
    }
}

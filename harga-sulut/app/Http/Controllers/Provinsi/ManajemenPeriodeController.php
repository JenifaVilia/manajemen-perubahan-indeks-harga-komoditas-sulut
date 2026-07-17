<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;

class ManajemenPeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::with('creator')->orderByDesc('tahun')->orderByDesc('bulan')->paginate(12);
        $periodeAktif = Periode::where('status', 'aktif')->first();
        return view('provinsi.manajemen.periode.index', compact('periodes', 'periodeAktif'));
    }

    public function create()
    {
        return view('provinsi.manajemen.periode.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bulan'         => ['required', 'integer', 'min:1', 'max:12'],
            'tahun'         => ['required', 'integer', 'min:2020', 'max:2030'],
            'tanggal_buka'  => ['nullable', 'date'],
            'tanggal_tutup' => ['nullable', 'date', 'after_or_equal:tanggal_buka'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ], [
            'bulan.required' => 'Bulan wajib diisi.',
            'tahun.required' => 'Tahun wajib diisi.',
        ]);

        // Cek duplikat
        if (Periode::where('bulan', $data['bulan'])->where('tahun', $data['tahun'])->exists()) {
            return back()->withErrors(['bulan' => 'Periode bulan/tahun ini sudah ada.'])->withInput();
        }

        $data['created_by'] = auth()->id();
        $data['status'] = 'draft';
        Periode::create($data);

        return redirect()->route('provinsi.periode.index')->with('success', 'Periode berhasil dibuat.');
    }

    public function edit(Periode $periode)
    {
        return view('provinsi.manajemen.periode.edit', compact('periode'));
    }

    public function update(Request $request, Periode $periode)
    {
        $data = $request->validate([
            'tanggal_buka'  => ['nullable', 'date'],
            'tanggal_tutup' => ['nullable', 'date', 'after_or_equal:tanggal_buka'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ]);

        $periode->update($data);
        return redirect()->route('provinsi.periode.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Periode $periode)
    {
        abort_if($periode->status === 'aktif', 422, 'Tidak dapat menghapus periode yang sedang aktif.');
        $periode->delete();
        return redirect()->route('provinsi.periode.index')->with('success', 'Periode berhasil dihapus.');
    }

    public function buka(Periode $periode)
    {
        abort_if(Periode::where('status', 'aktif')->exists(), 422, 'Sudah ada periode aktif. Tutup periode sebelumnya terlebih dahulu.');

        $periode->update([
            'status'       => 'aktif',
            'tanggal_buka' => $periode->tanggal_buka ?? now()->toDateString(),
        ]);

        // Broadcast notifikasi ke semua Kab/Kota
        $userIds = User::role('kabupaten_kota')->where('is_active', true)->pluck('id')->toArray();
        if (!empty($userIds)) {
            Notifikasi::kirim(
                $userIds,
                'Periode Rekonsiliasi Dibuka',
                "Periode rekonsiliasi {$periode->nama} telah dibuka." .
                ($periode->tanggal_tutup ? " Deadline input: {$periode->tanggal_tutup->format('d M Y')}." : ''),
                'periode_buka',
                $periode->id,
                Periode::class,
                route('wilayah.input-alasan.index')
            );
        }

        return redirect()->route('provinsi.periode.index')->with('success', "Periode {$periode->nama} berhasil dibuka dan notifikasi dikirim.");
    }

    public function tutup(Periode $periode)
    {
        abort_if($periode->status !== 'aktif', 422, 'Periode ini tidak sedang aktif.');

        $periode->update([
            'status'        => 'ditutup',
            'tanggal_tutup' => $periode->tanggal_tutup ?? now()->toDateString(),
        ]);

        // Broadcast notifikasi ke semua user
        $userIds = User::where('is_active', true)->pluck('id')->toArray();
        if (!empty($userIds)) {
            Notifikasi::kirim(
                $userIds,
                'Periode Rekonsiliasi Ditutup',
                "Periode rekonsiliasi {$periode->nama} telah ditutup. Terima kasih atas partisipasi semua wilayah.",
                'periode_tutup',
                $periode->id,
                Periode::class,
            );
        }

        return redirect()->route('provinsi.periode.index')->with('success', "Periode {$periode->nama} berhasil ditutup.");
    }
}

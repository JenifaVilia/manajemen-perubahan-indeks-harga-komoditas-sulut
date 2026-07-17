<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\KomoditasWilayah;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;

class PermintaanKomoditasController extends Controller
{
    public function index()
    {
        $permintaans = KomoditasWilayah::with(['komoditas', 'wilayah', 'requester'])
            ->pending()
            ->latest('requested_at')
            ->paginate(20);

        $riwayat = KomoditasWilayah::with(['komoditas', 'wilayah', 'requester', 'approver'])
            ->whereNotIn('status', ['aktif', 'pending_tambah', 'pending_hapus'])
            ->latest('approved_at')
            ->limit(20)
            ->get();

        return view('provinsi.manajemen.permintaan-komoditas.index', compact('permintaans', 'riwayat'));
    }

    public function approve(Request $request, KomoditasWilayah $kw)
    {
        $request->validate([
            'catatan_approval' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $kw->status === 'pending_tambah' ? 'aktif' : 'nonaktif';
        $aksi      = $kw->status === 'pending_tambah' ? 'penambahan' : 'penghapusan';

        $kw->update([
            'status'           => $newStatus,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        // Notif ke user wilayah
        $userIds = User::where('wilayah_id', $kw->wilayah_id)->pluck('id')->toArray();
        if (!empty($userIds)) {
            Notifikasi::kirim(
                $userIds,
                'Permintaan Komoditas Disetujui',
                "Permintaan {$aksi} komoditas {$kw->komoditas->nama_komoditas} telah disetujui.",
                'approval_komoditas',
                $kw->id,
                KomoditasWilayah::class,
                route('wilayah.komoditas.permintaan')
            );
        }

        return back()->with('success', "Permintaan {$aksi} komoditas berhasil disetujui.");
    }

    public function reject(Request $request, KomoditasWilayah $kw)
    {
        $request->validate([
            'catatan_approval' => ['required', 'string', 'min:5', 'max:500'],
        ], ['catatan_approval.required' => 'Alasan penolakan wajib diisi.']);

        $aksi = $kw->status === 'pending_tambah' ? 'penambahan' : 'penghapusan';

        $kw->update([
            'status'           => 'ditolak',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        $userIds = User::where('wilayah_id', $kw->wilayah_id)->pluck('id')->toArray();
        if (!empty($userIds)) {
            Notifikasi::kirim(
                $userIds,
                'Permintaan Komoditas Ditolak',
                "Permintaan {$aksi} komoditas {$kw->komoditas->nama_komoditas} ditolak. Alasan: {$request->catatan_approval}",
                'approval_komoditas',
                $kw->id,
                KomoditasWilayah::class,
                route('wilayah.komoditas.permintaan')
            );
        }

        return back()->with('success', "Permintaan {$aksi} komoditas berhasil ditolak.");
    }
}

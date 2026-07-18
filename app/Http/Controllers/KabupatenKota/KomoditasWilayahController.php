<?php

namespace App\Http\Controllers\KabupatenKota;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\KomoditasWilayah;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;

class KomoditasWilayahController extends Controller
{
    public function index()
    {
        $wilayah = auth()->user()->wilayah;

        $komoditasAktif = KomoditasWilayah::with('komoditas')
            ->where('wilayah_id', $wilayah->id)
            ->where('status', 'aktif')
            ->get();

        $komoditasTersedia = Komoditas::aktif()
            ->whereNotIn('id', $komoditasAktif->pluck('komoditas_id'))
            ->orderBy('kode_komoditas')
            ->get();

        return view('wilayah.komoditas.index', compact('komoditasAktif', 'komoditasTersedia', 'wilayah'));
    }

    public function ajukanTambah(Request $request)
    {
        $data = $request->validate([
            'komoditas_id'      => ['required', 'exists:komoditas,id'],
            'alasan_pengajuan'  => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'alasan_pengajuan.required' => 'Alasan pengajuan wajib diisi.',
            'alasan_pengajuan.min'      => 'Alasan minimal 10 karakter.',
        ]);

        $wilayah = auth()->user()->wilayah;

        // Cek duplikat
        $existing = KomoditasWilayah::where('wilayah_id', $wilayah->id)
            ->where('komoditas_id', $data['komoditas_id'])
            ->whereIn('status', ['aktif', 'pending_tambah'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Komoditas ini sudah aktif atau sedang dalam pengajuan.');
        }

        KomoditasWilayah::create([
            'komoditas_id'     => $data['komoditas_id'],
            'wilayah_id'       => $wilayah->id,
            'status'           => 'pending_tambah',
            'requested_at'     => now(),
            'requested_by'     => auth()->id(),
            'alasan_pengajuan' => $data['alasan_pengajuan'],
        ]);

        // Notif ke provinsi
        $provinsiUsers = User::role('provinsi')->pluck('id')->toArray();
        if (!empty($provinsiUsers)) {
            $komoditas = Komoditas::find($data['komoditas_id']);
            Notifikasi::kirim(
                $provinsiUsers,
                'Permintaan Tambah Komoditas',
                "{$wilayah->nama_wilayah} mengajukan penambahan komoditas {$komoditas->nama_komoditas}.",
                'approval_komoditas',
                null, null,
                route('provinsi.permintaan-komoditas.index')
            );
        }

        return back()->with('success', 'Permintaan penambahan komoditas berhasil diajukan.');
    }

    public function ajukanHapus(Request $request, KomoditasWilayah $kw)
    {
        abort_if($kw->wilayah_id !== auth()->user()->wilayah_id, 403);

        $request->validate([
            'alasan_pengajuan' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $kw->update([
            'status'           => 'pending_hapus',
            'requested_at'     => now(),
            'requested_by'     => auth()->id(),
            'alasan_pengajuan' => $request->alasan_pengajuan,
        ]);

        $provinsiUsers = User::role('provinsi')->pluck('id')->toArray();
        if (!empty($provinsiUsers)) {
            Notifikasi::kirim(
                $provinsiUsers,
                'Permintaan Hapus Komoditas',
                "{$kw->wilayah->nama_wilayah} mengajukan penghapusan komoditas {$kw->komoditas->nama_komoditas}.",
                'approval_komoditas',
                null, null,
                route('provinsi.permintaan-komoditas.index')
            );
        }

        return back()->with('success', 'Permintaan penghapusan komoditas berhasil diajukan.');
    }

    public function daftarPermintaan()
    {
        $wilayah = auth()->user()->wilayah;

        $permintaans = KomoditasWilayah::with(['komoditas', 'approver'])
            ->where('wilayah_id', $wilayah->id)
            ->whereNotIn('status', ['aktif'])
            ->latest('requested_at')
            ->paginate(20);

        return view('wilayah.komoditas.permintaan', compact('permintaans', 'wilayah'));
    }
}

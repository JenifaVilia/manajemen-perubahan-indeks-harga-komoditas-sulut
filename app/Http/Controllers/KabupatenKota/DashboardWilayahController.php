<?php

namespace App\Http\Controllers\KabupatenKota;

use App\Http\Controllers\Controller;
use App\Models\AlasanPerubahan;
use App\Models\DataHarga;
use App\Models\Periode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardWilayahController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $wilayah = $user->wilayah;
        $periode = Periode::where('status', 'aktif')->first()
            ?? Periode::where('status', 'ditutup')->latest()->first();

        // KPI
        $totalKomoditas = $wilayah->komoditasWilayah()->where('status', 'aktif')->count();

        $perluInput    = 0;
        $sudahInput    = 0;
        $persenSelesai = 0;
        $rataInflasi   = null;
        $dataHargas    = collect();
        $needsInput    = collect();
        $periodeAlerts = collect();

        if ($periode) {
            $perluInput = DataHarga::where('periode_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->signifikan()->count();

            $sudahInput = AlasanPerubahan::where('periode_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->whereIn('status', ['submitted', 'disetujui'])->count();

            $persenSelesai = $perluInput > 0 ? round($sudahInput / $perluInput * 100) : 100;

            $rataInflasi = DataHarga::where('periode_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->avg('inflasi_mtm');

            // Komoditas yang belum diisi alasan (signifikan tapi belum ada alasan submitted/disetujui)
            $alasanKomoditasIds = AlasanPerubahan::where('periode_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->whereIn('status', ['submitted', 'disetujui'])
                ->pluck('komoditas_id');

            $needsInput = DataHarga::with('komoditas')
                ->where('periode_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->signifikan()
                ->whereNotIn('komoditas_id', $alasanKomoditasIds)
                ->orderByRaw('ABS(inflasi_mtm) DESC')
                ->limit(10)
                ->get();

            // Alasan yang perlu revisi
            $periodeAlerts = AlasanPerubahan::with('komoditas')
                ->where('periode_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->where('status', 'revisi')
                ->get();
        }

        return view('wilayah.dashboard', compact(
            'wilayah', 'periode', 'totalKomoditas',
            'perluInput', 'sudahInput', 'persenSelesai',
            'rataInflasi', 'needsInput', 'periodeAlerts'
        ));
    }

    public function chartData(): JsonResponse
    {
        $wilayah = auth()->user()->wilayah;

        $periodes = Periode::whereIn('status', ['aktif', 'ditutup'])
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->limit(12)->get()->reverse()->values();

        $data = [];
        foreach ($periodes as $p) {
            $avg = DataHarga::where('periode_id', $p->id)
                ->where('wilayah_id', $wilayah->id)
                ->where('tipe_indeks', 'IHK')
                ->avg('inflasi_mtm');
            $data[] = $avg !== null ? round((float)$avg, 4) : null;
        }

        // Rata-rata provinsi untuk komparasi
        $dataProvinsi = [];
        foreach ($periodes as $p) {
            $avg = DataHarga::where('periode_id', $p->id)
                ->where('tipe_indeks', 'IHK')
                ->avg('inflasi_mtm');
            $dataProvinsi[] = $avg !== null ? round((float)$avg, 4) : null;
        }

        return response()->json([
            'labels'   => $periodes->map(fn($p) => $p->nama)->toArray(),
            'wilayah'  => ['label' => $wilayah->nama_wilayah, 'data' => $data],
            'provinsi' => ['label' => 'Rata-rata Provinsi',  'data' => $dataProvinsi],
        ]);
    }
}

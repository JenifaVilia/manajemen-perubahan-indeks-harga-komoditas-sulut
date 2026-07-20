<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\AlasanPerubahan;
use App\Models\DataHarga;
use App\Models\Komoditas;
use App\Models\Periode;
use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardProvinsiController extends Controller
{
    public function index(Request $request)
    {
        // Periode aktif atau terakhir ditutup
        $periodeAktif = Periode::where('status', 'aktif')->first()
            ?? Periode::where('status', 'ditutup')->latest()->first();

        // Semua Kab/Kota
        $wilayahs = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();

        // KPI — Total Komoditas Dipantau
        $totalKomoditas = Komoditas::aktif()->count();

        // KPI — Progress input alasan per wilayah (periode aktif)
        $statusWilayah = [];
        $totalPerluInput   = 0;
        $totalSudahInput   = 0;

        if ($periodeAktif) {
            foreach ($wilayahs as $w) {
                $komoditasSignifikan = DataHarga::where('periode_id', $periodeAktif->id)
                    ->where('wilayah_id', $w->id)
                    ->signifikan()
                    ->count();

                $sudahInput = AlasanPerubahan::where('periode_id', $periodeAktif->id)
                    ->where('wilayah_id', $w->id)
                    ->whereIn('status', ['submitted', 'disetujui'])
                    ->count();

                $statusWilayah[$w->id] = [
                    'wilayah'   => $w,
                    'perlu'     => $komoditasSignifikan,
                    'sudah'     => $sudahInput,
                    'persen'    => $komoditasSignifikan > 0 ? round($sudahInput / $komoditasSignifikan * 100) : 100,
                    'status'    => $this->getStatusWilayah($komoditasSignifikan, $sudahInput),
                ];

                $totalPerluInput += $komoditasSignifikan;
                $totalSudahInput += $sudahInput;
            }
        }

        $persenSelesai = $totalPerluInput > 0
            ? round($totalSudahInput / $totalPerluInput * 100)
            : 0;

        // KPI — Rata-rata inflasi MtM provinsi
        $rataInflasi = null;
        if ($periodeAktif) {
            $rataInflasi = DataHarga::where('periode_id', $periodeAktif->id)
                ->avg('inflasi_mtm');
        }

        // KPI — Alasan pending review
        $pendingReview = AlasanPerubahan::submitted()->count();

        // Top 5 Kenaikan & Penurunan Harga MtM (%)
        $topKenaikan = [];
        $topPenurunan = [];
        if ($periodeAktif) {
            $topKenaikan = DataHarga::with(['komoditas', 'wilayah'])
                ->where('periode_id', $periodeAktif->id)
                ->where('tipe_indeks', 'IHK')
                ->where('inflasi_mtm', '>', 0)
                ->orderByDesc('inflasi_mtm')
                ->limit(5)
                ->get();

            $topPenurunan = DataHarga::with(['komoditas', 'wilayah'])
                ->where('periode_id', $periodeAktif->id)
                ->where('tipe_indeks', 'IHK')
                ->where('inflasi_mtm', '<', 0)
                ->orderBy('inflasi_mtm')
                ->limit(5)
                ->get();
        }

        // Periode daftar untuk filter
        $periodes = Periode::orderByDesc('tahun')->orderByDesc('bulan')->limit(12)->get();

        return view('provinsi.dashboard', compact(
            'periodeAktif',
            'wilayahs',
            'statusWilayah',
            'totalKomoditas',
            'persenSelesai',
            'totalSudahInput',
            'totalPerluInput',
            'rataInflasi',
            'pendingReview',
            'topKenaikan',
            'topPenurunan',
            'periodes'
        ));
    }

    /**
     * JSON endpoint: data untuk peta choropleth (warna berdasarkan status rekonsiliasi)
     */
    public function petaData(Request $request): JsonResponse
    {
        $periodeId = $request->get('periode_id');
        $periode = $periodeId
            ? Periode::findOrFail($periodeId)
            : (Periode::where('status', 'aktif')->first() ?? Periode::orderByDesc('id')->first());

        if (!$periode) {
            return response()->json(['features' => []]);
        }

        $wilayahs = Wilayah::kabupatenKota()->aktif()->get();
        $features = [];

        foreach ($wilayahs as $w) {
            $perlu = DataHarga::where('periode_id', $periode->id)
                ->where('wilayah_id', $w->id)
                ->signifikan()->count();

            $sudah = AlasanPerubahan::where('periode_id', $periode->id)
                ->where('wilayah_id', $w->id)
                ->whereIn('status', ['submitted', 'disetujui'])->count();

            $persen = $perlu > 0 ? round($sudah / $perlu * 100) : 100;

            $features[] = [
                'kode'       => $w->kode_wilayah,
                'nama'       => $w->nama_wilayah,
                'tipe'       => $w->tipe,
                'perlu'      => $perlu,
                'sudah'      => $sudah,
                'persen'     => $persen,
                'status'     => $this->getStatusWilayah($perlu, $sudah),
            ];
        }

        return response()->json([
            'periode' => $periode->nama,
            'features' => $features,
        ]);
    }

    /**
     * JSON endpoint: data chart inflasi MtM per wilayah (12 bulan terakhir)
     */
    public function chartInflasi(Request $request): JsonResponse
    {
        $wilayahId  = $request->get('wilayah_id');
        $tipeIndeks = $request->get('tipe_indeks', 'IHK');
        $periodes   = Periode::whereIn('status', ['aktif', 'ditutup'])
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->limit(12)->get()->reverse()->values();

        if ($wilayahId) {
            // Tren satu wilayah
            $data = [];
            foreach ($periodes as $p) {
                $avg = DataHarga::where('periode_id', $p->id)
                    ->where('wilayah_id', $wilayahId)
                    ->where('tipe_indeks', $tipeIndeks)
                    ->avg('inflasi_mtm');
                $data[] = $avg !== null ? round((float)$avg, 4) : null;
            }
            return response()->json([
                'labels' => $periodes->map(fn($p) => $p->nama)->toArray(),
                'datasets' => [['label' => Wilayah::find($wilayahId)?->nama_wilayah, 'data' => $data]],
            ]);
        }

        // Semua wilayah
        $wilayahs = Wilayah::kabupatenKota()->aktif()->get();
        $datasets = [];
        $colors = ['#006699','#fecb00','#1a6b3a','#ba1a1a','#124d6f','#745b00','#316589','#90cdff','#d3e4fe','#cbdbf5','#213145','#40484f','#707880','#c0c7d0','#0b1c30'];

        foreach ($wilayahs as $i => $w) {
            $data = [];
            foreach ($periodes as $p) {
                $avg = DataHarga::where('periode_id', $p->id)
                    ->where('wilayah_id', $w->id)
                    ->where('tipe_indeks', $tipeIndeks)
                    ->avg('inflasi_mtm');
                $data[] = $avg !== null ? round((float)$avg, 4) : null;
            }
            $datasets[] = ['label' => $w->nama_wilayah, 'data' => $data, 'color' => $colors[$i % count($colors)]];
        }

        return response()->json([
            'labels'   => $periodes->map(fn($p) => $p->nama)->toArray(),
            'datasets' => $datasets,
        ]);
    }

    private function getStatusWilayah(int $perlu, int $sudah): string
    {
        if ($perlu === 0) return 'no-data';
        if ($sudah === 0) return 'belum';
        if ($sudah >= $perlu) return 'selesai';
        return 'sebagian';
    }
}

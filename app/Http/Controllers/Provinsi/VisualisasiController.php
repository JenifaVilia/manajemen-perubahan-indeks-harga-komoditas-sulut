<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\DataHarga;
use App\Models\Komoditas;
use App\Models\Periode;
use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisualisasiController extends Controller
{
    /**
     * Tabel Relatif Harga — pivot komoditas × wilayah
     */
    public function tabelRelatif(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodes     = Periode::orderByDesc('tahun')->orderByDesc('bulan')->limit(24)->get();
        $wilayahs     = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        $komoditas    = Komoditas::aktif()->orderBy('kode_komoditas')->get();

        $periodeId = $request->get('periode_id', $periodeAktif?->id);
        $metrik    = $request->get('metrik', 'inflasi_mtm'); // inflasi_mtm, inflasi_ytd, inflasi_yoy, harga_level

        // Build pivot: komoditas_id => [wilayah_id => nilai]
        $pivot = [];
        if ($periodeId) {
            $data = DataHarga::where('periode_id', $periodeId)->get();
            foreach ($data as $d) {
                $pivot[$d->komoditas_id][$d->wilayah_id] = $d->{$metrik};
            }
        }

        return view('provinsi.visualisasi.tabel-relatif', compact(
            'periodes', 'wilayahs', 'komoditas', 'periodeAktif',
            'periodeId', 'metrik', 'pivot'
        ));
    }

    /**
     * Output MtM — heat-map style semua komoditas × wilayah
     */
    public function outputMtm(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodes     = Periode::orderByDesc('tahun')->orderByDesc('bulan')->limit(24)->get();
        $wilayahs     = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        $komoditas    = Komoditas::aktif()->orderBy('kode_komoditas')->get();

        $periodeId = $request->get('periode_id', $periodeAktif?->id);

        $pivot = [];
        $stats = ['max' => 0, 'min' => 0, 'avg' => 0, 'naik' => 0, 'turun' => 0, 'stabil' => 0];

        if ($periodeId) {
            $data = DataHarga::where('periode_id', $periodeId)->get();
            $inflasi = $data->pluck('inflasi_mtm')->map(fn($v) => (float)$v);

            $stats['max']    = $inflasi->max() ?? 0;
            $stats['min']    = $inflasi->min() ?? 0;
            $stats['avg']    = round($inflasi->avg() ?? 0, 4);
            $stats['naik']   = $inflasi->filter(fn($v) => $v > 0)->count();
            $stats['turun']  = $inflasi->filter(fn($v) => $v < 0)->count();
            $stats['stabil'] = $inflasi->filter(fn($v) => $v == 0)->count();

            foreach ($data as $d) {
                $pivot[$d->komoditas_id][$d->wilayah_id] = (float) $d->inflasi_mtm;
            }
        }

        return view('provinsi.visualisasi.output-mtm', compact(
            'periodes', 'wilayahs', 'komoditas', 'periodeAktif',
            'periodeId', 'pivot', 'stats'
        ));
    }

    /**
     * Tren Komoditas — line chart komoditas tertentu di semua wilayah selama 12 bulan
     */
    public function trenKomoditas(Request $request)
    {
        $komoditasList = Komoditas::aktif()->orderBy('kode_komoditas')->get();
        $wilayahs      = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        $periodes      = Periode::whereIn('status', ['aktif', 'ditutup'])
                            ->orderByDesc('tahun')->orderByDesc('bulan')
                            ->limit(12)->get()->reverse()->values();

        $komoditasId = $request->get('komoditas_id', $komoditasList->first()?->id);
        $komoditas   = $komoditasId ? Komoditas::find($komoditasId) : null;

        return view('provinsi.visualisasi.tren-komoditas', compact(
            'komoditasList', 'wilayahs', 'periodes', 'komoditasId', 'komoditas'
        ));
    }

    /**
     * JSON endpoint: data tren komoditas untuk chart
     */
    public function dataTabel(Request $request): JsonResponse
    {
        $komoditasId = $request->get('komoditas_id');
        $wilayahId   = $request->get('wilayah_id');

        $periodes = Periode::whereIn('status', ['aktif', 'ditutup'])
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->limit(12)->get()->reverse()->values();

        $labels = $periodes->map(fn($p) => $p->nama)->toArray();

        if ($komoditasId) {
            // Tren satu komoditas di semua wilayah
            $wilayahs = Wilayah::kabupatenKota()->aktif()->get();
            $colors = ['#006699','#fecb00','#1a6b3a','#ba1a1a','#124d6f','#745b00','#316589','#90cdff','#d3e4fe','#cbdbf5','#213145','#40484f','#707880','#c0c7d0','#0b1c30'];
            $datasets = [];

            foreach ($wilayahs as $i => $w) {
                $data = [];
                foreach ($periodes as $p) {
                    $val = DataHarga::where('periode_id', $p->id)
                        ->where('wilayah_id', $w->id)
                        ->where('komoditas_id', $komoditasId)
                        ->value('inflasi_mtm');
                    $data[] = $val !== null ? round((float)$val, 4) : null;
                }
                $datasets[] = [
                    'label' => $w->nama_wilayah,
                    'data'  => $data,
                    'color' => $colors[$i % count($colors)],
                ];
            }

            return response()->json(compact('labels', 'datasets'));
        }

        // Semua komoditas di satu wilayah
        if ($wilayahId) {
            $komoditasList = Komoditas::aktif()->get();
            $datasets = [];

            foreach ($komoditasList as $k) {
                $data = [];
                foreach ($periodes as $p) {
                    $val = DataHarga::where('periode_id', $p->id)
                        ->where('wilayah_id', $wilayahId)
                        ->where('komoditas_id', $k->id)
                        ->value('inflasi_mtm');
                    $data[] = $val !== null ? round((float)$val, 4) : null;
                }
                $datasets[] = ['label' => $k->nama_komoditas, 'data' => $data];
            }

            return response()->json(compact('labels', 'datasets'));
        }

        return response()->json(['labels' => $labels, 'datasets' => []]);
    }
}

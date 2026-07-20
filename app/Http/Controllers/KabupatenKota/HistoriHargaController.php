<?php

namespace App\Http\Controllers\KabupatenKota;

use App\Http\Controllers\Controller;
use App\Models\DataHarga;
use App\Models\Komoditas;
use App\Models\Periode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoriHargaController extends Controller
{
    public function index(Request $request)
    {
        $wilayah   = auth()->user()->wilayah;
        $periodes  = Periode::orderByDesc('tahun')->orderByDesc('bulan')->limit(24)->get();
        $komoditas = Komoditas::aktif()->orderBy('kode_komoditas')->get();

        $periodeId   = $request->get('periode_id');
        $komoditasId = $request->get('komoditas_id');
        $tipeIndeks  = $request->get('tipe_indeks', 'IHK');

        $query = DataHarga::with(['komoditas', 'periode'])
            ->where('wilayah_id', $wilayah->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->when($komoditasId, fn($q) => $q->where('komoditas_id', $komoditasId))
            ->when($tipeIndeks, fn($q) => $q->where('tipe_indeks', $tipeIndeks))
            ->orderByDesc('periode_id');

        $dataHargas = $query->paginate(30)->withQueryString();

        return view('wilayah.histori.index', compact('dataHargas', 'periodes', 'komoditas', 'periodeId', 'komoditasId', 'tipeIndeks', 'wilayah'));
    }

    public function chartData(Request $request): JsonResponse
    {
        $wilayah     = auth()->user()->wilayah;
        $komoditasId = $request->get('komoditas_id');

        $periodes = Periode::whereIn('status', ['aktif', 'ditutup'])
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->limit(12)->get()->reverse()->values();

        $labels = $periodes->map(fn($p) => $p->nama)->toArray();

        if ($komoditasId) {
            $data = [];
            foreach ($periodes as $p) {
                $val = DataHarga::where('periode_id', $p->id)
                    ->where('wilayah_id', $wilayah->id)
                    ->where('komoditas_id', $komoditasId)
                    ->value('inflasi_mtm');
                $data[] = $val !== null ? round((float)$val, 4) : null;
            }
            return response()->json([
                'labels' => $labels,
                'datasets' => [
                    ['label' => Komoditas::find($komoditasId)?->nama_komoditas, 'data' => $data, 'color' => '#006699']
                ],
            ]);
        }

        // Rata-rata semua komoditas
        $data = [];
        foreach ($periodes as $p) {
            $avg = DataHarga::where('periode_id', $p->id)
                ->where('wilayah_id', $wilayah->id)
                ->avg('inflasi_mtm');
            $data[] = $avg !== null ? round((float)$avg, 4) : null;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Rata-rata Inflasi MtM', 'data' => $data, 'color' => '#006699']
            ],
        ]);
    }
}

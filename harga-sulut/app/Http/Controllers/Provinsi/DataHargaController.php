<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\DataHarga;
use App\Models\Komoditas;
use App\Models\Periode;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataHargaController extends Controller
{
    public function index()
    {
        $periodes = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        $wilayahs = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        return view('provinsi.data-harga.index', compact('periodes', 'wilayahs'));
    }

    public function formUpload()
    {
        $periodes = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        return view('provinsi.data-harga.upload', compact('periodes'));
    }

    public function prosesUpload(Request $request)
    {
        $request->validate([
            'periode_id' => ['required', 'exists:periodes,id'],
            'wilayah_id' => ['required', 'exists:wilayahs,id'],
            'tipe_indeks'=> ['required', 'in:IHK,IHPB,IPP,IPH'],
            'file'       => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        // TODO: Parse Excel dengan PhpSpreadsheet
        // Placeholder: return with success
        return redirect()->route('provinsi.data-harga.riwayat')
            ->with('success', 'File Excel berhasil diupload dan diproses. (Fitur parsing sedang dikembangkan)');
    }

    public function formManual()
    {
        $periodes   = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        $wilayahs   = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        $komoditas  = Komoditas::aktif()->orderBy('kode_komoditas')->get();
        return view('provinsi.data-harga.manual', compact('periodes', 'wilayahs', 'komoditas'));
    }

    public function simpanManual(Request $request)
    {
        $data = $request->validate([
            'periode_id'   => ['required', 'exists:periodes,id'],
            'wilayah_id'   => ['required', 'exists:wilayahs,id'],
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'tipe_indeks'  => ['required', 'in:IHK,IHPB,IPP,IPH'],
            'harga_level'  => ['required', 'numeric', 'min:0'],
            'inflasi_mtm'  => ['nullable', 'numeric'],
            'inflasi_ytd'  => ['nullable', 'numeric'],
            'inflasi_yoy'  => ['nullable', 'numeric'],
            'andil_mtm'    => ['nullable', 'numeric'],
            'andil_ytd'    => ['nullable', 'numeric'],
            'andil_yoy'    => ['nullable', 'numeric'],
        ]);

        DataHarga::updateOrCreate(
            [
                'periode_id'   => $data['periode_id'],
                'wilayah_id'   => $data['wilayah_id'],
                'komoditas_id' => $data['komoditas_id'],
                'tipe_indeks'  => $data['tipe_indeks'],
            ],
            [
                ...$data,
                'uploaded_by' => auth()->id(),
            ]
        );

        return redirect()->route('provinsi.data-harga.index')
            ->with('success', 'Data harga berhasil disimpan.');
    }

    public function riwayat()
    {
        $riwayat = DataHarga::with(['periode', 'wilayah', 'komoditas', 'uploader'])
            ->latest()
            ->paginate(25);
        return view('provinsi.data-harga.riwayat', compact('riwayat'));
    }

    public function downloadTemplate()
    {
        // Generate simple Excel template
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Harga');

        $headers = ['Kode Komoditas', 'Nama Komoditas', 'Satuan', 'Kelompok', 'Harga Level', 'Inflasi MtM (%)', 'Inflasi YtD (%)', 'Inflasi YoY (%)', 'Andil MtM', 'Andil YtD', 'Andil YoY'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        $komoditas = Komoditas::aktif()->orderBy('kode_komoditas')->get();
        foreach ($komoditas as $i => $k) {
            $row = $i + 2;
            $sheet->setCellValueByColumnAndRow(1, $row, $k->kode_komoditas);
            $sheet->setCellValueByColumnAndRow(2, $row, $k->nama_komoditas);
            $sheet->setCellValueByColumnAndRow(3, $row, $k->satuan);
            $sheet->setCellValueByColumnAndRow(4, $row, $k->kelompok);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template-harga-sulut-' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

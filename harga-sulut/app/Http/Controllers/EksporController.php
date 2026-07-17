<?php

namespace App\Http\Controllers;

use App\Models\AlasanPerubahan;
use App\Models\DataHarga;
use App\Models\Periode;
use App\Models\Wilayah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EksporController extends Controller
{
    public function alasanProvinsi(Request $request)
    {
        return $this->eksporAlasan($request, null);
    }

    public function alasanWilayah(Request $request)
    {
        return $this->eksporAlasan($request, auth()->user()->wilayah_id);
    }

    private function eksporAlasan(Request $request, ?int $wilayahId)
    {
        $periodeId = $request->get('periode_id', Periode::where('status','aktif')->first()?->id);
        $format    = $request->get('format', 'excel');

        $query = AlasanPerubahan::with(['komoditas','wilayah','periode','submitter','reviewer'])
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->when($wilayahId, fn($q) => $q->where('wilayah_id', $wilayahId))
            ->orderBy('wilayah_id')
            ->orderBy('komoditas_id');

        $alasans = $query->get();
        $periode = $periodeId ? Periode::find($periodeId) : null;

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.alasan-pdf', compact('alasans', 'periode', 'wilayahId'))
                ->setPaper('a4', 'landscape');
            return $pdf->download("alasan-perubahan-harga-{$periode?->nama}.pdf");
        }

        // Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Alasan Perubahan Harga');

        // Header
        $headers = ['No', 'Wilayah', 'Komoditas', 'Kelompok', 'Periode', 'MtM (%)', 'Alasan', 'Faktor Pendorong', 'Rekomendasi', 'Status', 'Diinput Oleh', 'Tanggal Submit', 'Direview Oleh', 'Catatan Provinsi'];
        foreach ($headers as $i => $h) {
            $cell = $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        // Style header
        $sheet->getStyle('A1:' . $sheet->getCellByColumnAndRow(count($headers), 1)->getCoordinate())
            ->getFont()->setBold(true);

        foreach ($alasans as $i => $a) {
            $row = $i + 2;
            $sheet->setCellValueByColumnAndRow(1,  $row, $i + 1);
            $sheet->setCellValueByColumnAndRow(2,  $row, $a->wilayah->nama_wilayah);
            $sheet->setCellValueByColumnAndRow(3,  $row, $a->komoditas->nama_komoditas);
            $sheet->setCellValueByColumnAndRow(4,  $row, $a->komoditas->kelompok);
            $sheet->setCellValueByColumnAndRow(5,  $row, $a->periode->nama);
            $sheet->setCellValueByColumnAndRow(7,  $row, $a->alasan);
            $sheet->setCellValueByColumnAndRow(8,  $row, $a->faktors_label);
            $sheet->setCellValueByColumnAndRow(9,  $row, $a->rekomendasi ?? '');
            $sheet->setCellValueByColumnAndRow(10, $row, $a->status_badge['label']);
            $sheet->setCellValueByColumnAndRow(11, $row, $a->submitter?->name ?? '-');
            $sheet->setCellValueByColumnAndRow(12, $row, $a->submitted_at?->format('d/m/Y H:i') ?? '-');
            $sheet->setCellValueByColumnAndRow(13, $row, $a->reviewer?->name ?? '-');
            $sheet->setCellValueByColumnAndRow(14, $row, $a->catatan_provinsi ?? '');
        }

        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "alasan-harga-{$periode?->nama}-" . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(fn() => $writer->save('php://output'), $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function tabelRelatif(Request $request)
    {
        $periodeId = $request->get('periode_id', Periode::where('status','aktif')->first()?->id);
        $periode   = $periodeId ? Periode::find($periodeId) : null;

        $wilayahs  = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tabel Relatif Harga');

        // Header wilayah
        $sheet->setCellValueByColumnAndRow(1, 1, 'Komoditas');
        foreach ($wilayahs as $i => $w) {
            $sheet->setCellValueByColumnAndRow($i + 2, 1, $w->nama_wilayah);
        }

        $komoditas = \App\Models\Komoditas::aktif()->orderBy('kode_komoditas')->get();
        foreach ($komoditas as $ki => $k) {
            $row = $ki + 2;
            $sheet->setCellValueByColumnAndRow(1, $row, $k->nama_komoditas);
            foreach ($wilayahs as $wi => $w) {
                $dh = DataHarga::where('periode_id', $periodeId)
                    ->where('wilayah_id', $w->id)
                    ->where('komoditas_id', $k->id)
                    ->first();
                $sheet->setCellValueByColumnAndRow($wi + 2, $row, $dh ? round((float)$dh->inflasi_mtm, 4) : '');
            }
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = "tabel-relatif-{$periode?->nama}-" . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(fn() => $writer->save('php://output'), $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function rekapPeriode(Request $request)
    {
        $periodeId = $request->get('periode_id', Periode::where('status','aktif')->first()?->id);
        $periode   = $periodeId ? Periode::with('creator')->find($periodeId) : null;
        $wilayahs  = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();

        $statusWilayah = [];
        if ($periode) {
            foreach ($wilayahs as $w) {
                $perlu = DataHarga::where('periode_id', $periode->id)->where('wilayah_id', $w->id)->signifikan()->count();
                $sudah = AlasanPerubahan::where('periode_id', $periode->id)->where('wilayah_id', $w->id)->whereIn('status', ['submitted','disetujui'])->count();
                $statusWilayah[] = ['wilayah' => $w, 'perlu' => $perlu, 'sudah' => $sudah, 'persen' => $perlu > 0 ? round($sudah/$perlu*100) : 100];
            }
        }

        $pdf = Pdf::loadView('exports.rekap-periode-pdf', compact('periode', 'statusWilayah'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("rekap-rekonsiliasi-{$periode?->nama}.pdf");
    }

    public function historiWilayah(Request $request)
    {
        $wilayah = auth()->user()->wilayah;
        $data    = DataHarga::with(['komoditas', 'periode'])
            ->where('wilayah_id', $wilayah->id)
            ->orderBy('periode_id', 'desc')
            ->orderBy('komoditas_id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Histori Harga');

        $headers = ['Periode', 'Komoditas', 'Harga Level', 'MtM (%)', 'YtD (%)', 'YoY (%)', 'Andil MtM'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        foreach ($data as $i => $d) {
            $row = $i + 2;
            $sheet->setCellValueByColumnAndRow(1, $row, $d->periode->nama);
            $sheet->setCellValueByColumnAndRow(2, $row, $d->komoditas->nama_komoditas);
            $sheet->setCellValueByColumnAndRow(3, $row, $d->harga_level);
            $sheet->setCellValueByColumnAndRow(4, $row, $d->inflasi_mtm);
            $sheet->setCellValueByColumnAndRow(5, $row, $d->inflasi_ytd);
            $sheet->setCellValueByColumnAndRow(6, $row, $d->inflasi_yoy);
            $sheet->setCellValueByColumnAndRow(7, $row, $d->andil_mtm);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "histori-harga-{$wilayah->nama_wilayah}-" . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(fn() => $writer->save('php://output'), $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

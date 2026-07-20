<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\DataHarga;
use App\Models\Komoditas;
use App\Models\Periode;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        $wilayahs = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        return view('provinsi.data-harga.upload', compact('periodes', 'wilayahs'));
    }

    public function prosesUpload(Request $request)
    {
        $request->validate([
            'periode_id' => ['required', 'exists:periodes,id'],
            'wilayah_id' => ['required', 'exists:wilayahs,id'],
            'tipe_indeks'=> ['required', 'in:IHK,IHPB,IPP,IPH'],
            'file'       => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $file = $request->file('file');
        $periodeId  = $request->input('periode_id');
        $wilayahId  = $request->input('wilayah_id');
        $tipeIndeks = $request->input('tipe_indeks');

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            // Get unformatted cell values so PhpSpreadsheet doesn't format numbers into strings like "15,500"
            $rows  = $sheet->toArray(null, true, false, true);

            $parseNumber = function($val) {
                if ($val === null || $val === '') return null;
                if (is_numeric($val)) return (float) $val;
                $clean = str_replace([' ', 'Rp', 'rp'], '', trim((string)$val));
                if (preg_match('/^\d{1,3}(\.\d{3})*,\d+$/', $clean)) {
                    $clean = str_replace('.', '', $clean);
                    $clean = str_replace(',', '.', $clean);
                } else {
                    $clean = str_replace(',', '', $clean);
                }
                return is_numeric($clean) ? (float) $clean : null;
            };

            // Find header row (search for 'Kode Komoditas' or 'kode_komoditas')
            $headerRow = null;
            $headerIdx = 0;
            foreach ($rows as $idx => $row) {
                $rowValues = array_map(fn($v) => strtolower(trim((string)($v ?? ''))), $row);
                if (in_array('kode komoditas', $rowValues) || in_array('kode_komoditas', $rowValues)) {
                    $headerRow = $row;
                    $headerIdx = $idx;
                    break;
                }
            }

            if (!$headerRow) {
                return back()->with('error', 'Format Excel tidak valid: kolom "Kode Komoditas" tidak ditemukan di baris header.');
            }

            // Map column letters to field names
            $columnMap = [];
            $fieldAliases = [
                'kode komoditas' => 'kode_komoditas',
                'kode_komoditas' => 'kode_komoditas',
                'harga level'    => 'harga_level',
                'harga_level'    => 'harga_level',
                'inflasi mtm'    => 'inflasi_mtm',
                'inflasi_mtm'    => 'inflasi_mtm',
                'inflasi mtm (%)' => 'inflasi_mtm',
                'inflasi ytd'    => 'inflasi_ytd',
                'inflasi_ytd'    => 'inflasi_ytd',
                'inflasi ytd (%)' => 'inflasi_ytd',
                'inflasi yoy'    => 'inflasi_yoy',
                'inflasi_yoy'    => 'inflasi_yoy',
                'inflasi yoy (%)' => 'inflasi_yoy',
                'andil mtm'      => 'andil_mtm',
                'andil_mtm'      => 'andil_mtm',
                'andil ytd'      => 'andil_ytd',
                'andil_ytd'      => 'andil_ytd',
                'andil yoy'      => 'andil_yoy',
                'andil_yoy'      => 'andil_yoy',
            ];

            foreach ($headerRow as $col => $val) {
                $normalized = strtolower(trim((string)($val ?? '')));
                if (isset($fieldAliases[$normalized])) {
                    $columnMap[$fieldAliases[$normalized]] = $col;
                }
            }

            if (!isset($columnMap['kode_komoditas'])) {
                return back()->with('error', 'Kolom "Kode Komoditas" tidak ditemukan.');
            }
            if (!isset($columnMap['harga_level'])) {
                return back()->with('error', 'Kolom "Harga Level" tidak ditemukan.');
            }

            // Load valid komoditas codes
            $validKomoditas = Komoditas::aktif()->pluck('id', 'kode_komoditas');

            $imported = 0;
            $skipped  = 0;
            $errors   = [];

            foreach ($rows as $idx => $row) {
                if ($idx <= $headerIdx) continue; // Skip header and above

                $kode = trim((string)($row[$columnMap['kode_komoditas']] ?? ''));
                if (empty($kode)) continue; // Skip empty rows

                if (strlen($kode) < 3 && is_numeric($kode)) {
                    $kode = str_pad($kode, 3, '0', STR_PAD_LEFT);
                }

                // Validate komoditas exists
                if (!$validKomoditas->has($kode)) {
                    $skipped++;
                    $errors[] = "Baris {$idx}: Kode komoditas '{$kode}' tidak ditemukan di database.";
                    continue;
                }

                $hargaLevelRaw = $row[$columnMap['harga_level']] ?? null;
                $hargaLevel    = $parseNumber($hargaLevelRaw);
                if ($hargaLevel === null || $hargaLevel < 0) {
                    $skipped++;
                    $errors[] = "Baris {$idx}: Harga level untuk kode '{$kode}' tidak valid.";
                    continue;
                }

                $komoditasId = $validKomoditas[$kode];

                DataHarga::updateOrCreate(
                    [
                        'periode_id'   => $periodeId,
                        'wilayah_id'   => $wilayahId,
                        'komoditas_id' => $komoditasId,
                        'tipe_indeks'  => $tipeIndeks,
                    ],
                    [
                        'harga_level' => $hargaLevel,
                        'inflasi_mtm' => isset($columnMap['inflasi_mtm']) ? $parseNumber($row[$columnMap['inflasi_mtm']] ?? null) : null,
                        'inflasi_ytd' => isset($columnMap['inflasi_ytd']) ? $parseNumber($row[$columnMap['inflasi_ytd']] ?? null) : null,
                        'inflasi_yoy' => isset($columnMap['inflasi_yoy']) ? $parseNumber($row[$columnMap['inflasi_yoy']] ?? null) : null,
                        'andil_mtm'   => isset($columnMap['andil_mtm'])   ? $parseNumber($row[$columnMap['andil_mtm']]   ?? null) : null,
                        'andil_ytd'   => isset($columnMap['andil_ytd'])   ? $parseNumber($row[$columnMap['andil_ytd']]   ?? null) : null,
                        'andil_yoy'   => isset($columnMap['andil_yoy'])   ? $parseNumber($row[$columnMap['andil_yoy']]   ?? null) : null,
                        'uploaded_by' => auth()->id(),
                        'sumber_file' => $file->getClientOriginalName(),
                    ]
                );
                $imported++;
            }

            $message = "{$imported} data harga berhasil diimpor dari file '{$file->getClientOriginalName()}'.";
            if ($skipped > 0) {
                $message .= " {$skipped} baris dilewati karena error.";
            }

            if (!empty($errors)) {
                session()->flash('import_errors', array_slice($errors, 0, 10));
            }

            return redirect()->route('provinsi.data-harga.riwayat')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
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

    public function riwayat(Request $request)
    {
        $periodes  = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        $wilayahs  = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();

        $periodeId  = $request->get('periode_id');
        $wilayahId  = $request->get('wilayah_id');
        $tipeIndeks = $request->get('tipe_indeks');
        $search     = $request->get('search');

        $query = DataHarga::with(['periode', 'wilayah', 'komoditas', 'uploader']);

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }
        if ($wilayahId) {
            $query->where('wilayah_id', $wilayahId);
        }
        if ($tipeIndeks) {
            $query->where('tipe_indeks', $tipeIndeks);
        }
        if ($search) {
            $query->whereHas('komoditas', function ($q) use ($search) {
                $q->where('nama_komoditas', 'like', "%{$search}%")
                  ->orWhere('kode_komoditas', 'like', "%{$search}%");
            });
        }

        $riwayat = $query->latest()->paginate(25)->withQueryString();

        return view('provinsi.data-harga.riwayat', compact(
            'riwayat', 'periodes', 'wilayahs', 'periodeId', 'wilayahId', 'tipeIndeks', 'search'
        ));
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

        // Style header row
        $lastCol = chr(64 + count($headers));
        $headerRange = "A1:{$lastCol}1";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D3E4FE');

        $komoditas = Komoditas::aktif()->orderBy('kode_komoditas')->get();
        foreach ($komoditas as $i => $k) {
            $row = $i + 2;
            $sheet->setCellValueByColumnAndRow(1, $row, $k->kode_komoditas);
            $sheet->setCellValueByColumnAndRow(2, $row, $k->nama_komoditas);
            $sheet->setCellValueByColumnAndRow(3, $row, $k->satuan);
            $sheet->setCellValueByColumnAndRow(4, $row, $k->kelompok);
        }

        // Auto-size columns
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
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

<?php

namespace Database\Seeders;

use App\Models\AlasanPerubahan;
use App\Models\DataHarga;
use App\Models\Komoditas;
use App\Models\KomoditasWilayah;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat roles
        $roleProvinsi = Role::firstOrCreate(['name' => 'provinsi']);
        $roleKabkota  = Role::firstOrCreate(['name' => 'kabupaten_kota']);

        // 2. Seed Wilayah Provinsi Sulawesi Utara
        $provinsi = Wilayah::firstOrCreate(
            ['kode_wilayah' => '71'],
            [
                'nama_wilayah' => 'Sulawesi Utara',
                'tipe'         => 'provinsi',
                'is_active'    => true,
            ]
        );

        // 15 Kabupaten/Kota Sulawesi Utara
        $wilayahs = [
            ['kode' => '7101', 'nama' => 'Bolaang Mongondow',         'tipe' => 'kabupaten'],
            ['kode' => '7102', 'nama' => 'Minahasa',                  'tipe' => 'kabupaten'],
            ['kode' => '7103', 'nama' => 'Kepulauan Sangihe',         'tipe' => 'kabupaten'],
            ['kode' => '7104', 'nama' => 'Kepulauan Talaud',          'tipe' => 'kabupaten'],
            ['kode' => '7105', 'nama' => 'Minahasa Selatan',          'tipe' => 'kabupaten'],
            ['kode' => '7106', 'nama' => 'Minahasa Utara',            'tipe' => 'kabupaten'],
            ['kode' => '7107', 'nama' => 'Bolaang Mongondow Utara',   'tipe' => 'kabupaten'],
            ['kode' => '7108', 'nama' => 'Kepulauan Siau Tagulandang Biaro', 'tipe' => 'kabupaten'],
            ['kode' => '7109', 'nama' => 'Minahasa Tenggara',         'tipe' => 'kabupaten'],
            ['kode' => '7110', 'nama' => 'Bolaang Mongondow Selatan', 'tipe' => 'kabupaten'],
            ['kode' => '7111', 'nama' => 'Bolaang Mongondow Timur',   'tipe' => 'kabupaten'],
            ['kode' => '7171', 'nama' => 'Manado',                    'tipe' => 'kota'],
            ['kode' => '7172', 'nama' => 'Bitung',                    'tipe' => 'kota'],
            ['kode' => '7173', 'nama' => 'Tomohon',                   'tipe' => 'kota'],
            ['kode' => '7174', 'nama' => 'Kotamobagu',                'tipe' => 'kota'],
        ];

        $createdWilayahs = [];
        foreach ($wilayahs as $w) {
            $createdWilayahs[$w['kode']] = Wilayah::firstOrCreate(
                ['kode_wilayah' => $w['kode']],
                [
                    'nama_wilayah' => $w['nama'],
                    'tipe'         => $w['tipe'],
                    'parent_id'    => $provinsi->id,
                    'is_active'    => true,
                ]
            );
        }

        // 3. User Provinsi
        $userProvinsi = User::firstOrCreate(
            ['email' => 'provinsi@bps-sulut.go.id'],
            [
                'name'      => 'BPS Provinsi Sulawesi Utara',
                'password'  => Hash::make('password'),
                'wilayah_id'=> $provinsi->id,
                'is_active' => true,
            ]
        );
        $userProvinsi->assignRole($roleProvinsi);

        // 4. User tiap Kabupaten/Kota
        $kabkotaUsers = [
            '7101' => ['nama' => 'BPS Kab. Bolaang Mongondow',         'email' => 'bolmong@bps-sulut.go.id'],
            '7102' => ['nama' => 'BPS Kab. Minahasa',                  'email' => 'minahasa@bps-sulut.go.id'],
            '7103' => ['nama' => 'BPS Kab. Kep. Sangihe',              'email' => 'sangihe@bps-sulut.go.id'],
            '7104' => ['nama' => 'BPS Kab. Kep. Talaud',               'email' => 'talaud@bps-sulut.go.id'],
            '7105' => ['nama' => 'BPS Kab. Minahasa Selatan',          'email' => 'minsel@bps-sulut.go.id'],
            '7106' => ['nama' => 'BPS Kab. Minahasa Utara',            'email' => 'minut@bps-sulut.go.id'],
            '7107' => ['nama' => 'BPS Kab. Bolaang Mongondow Utara',   'email' => 'bolmut@bps-sulut.go.id'],
            '7108' => ['nama' => 'BPS Kab. Kep. Sitaro',               'email' => 'sitaro@bps-sulut.go.id'],
            '7109' => ['nama' => 'BPS Kab. Minahasa Tenggara',         'email' => 'mitra@bps-sulut.go.id'],
            '7110' => ['nama' => 'BPS Kab. Bolaang Mongondow Selatan', 'email' => 'bolmongsel@bps-sulut.go.id'],
            '7111' => ['nama' => 'BPS Kab. Bolaang Mongondow Timur',   'email' => 'boltim@bps-sulut.go.id'],
            '7171' => ['nama' => 'BPS Kota Manado',                    'email' => 'manado@bps-sulut.go.id'],
            '7172' => ['nama' => 'BPS Kota Bitung',                    'email' => 'bitung@bps-sulut.go.id'],
            '7173' => ['nama' => 'BPS Kota Tomohon',                   'email' => 'tomohon@bps-sulut.go.id'],
            '7174' => ['nama' => 'BPS Kota Kotamobagu',                'email' => 'kotamobagu@bps-sulut.go.id'],
        ];

        $usersByWilayahKode = [];
        foreach ($kabkotaUsers as $kode => $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name'       => $u['nama'],
                    'password'   => Hash::make('password'),
                    'wilayah_id' => $createdWilayahs[$kode]->id,
                    'is_active'  => true,
                ]
            );
            $user->assignRole($roleKabkota);
            $usersByWilayahKode[$kode] = $user;
        }

        // 5. Master Komoditas IHK (sampel awal)
        $masterKomoditas = [
            // Makanan
            ['kode' => '001', 'nama' => 'Beras',                  'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Padi-padian'],
            ['kode' => '002', 'nama' => 'Daging Ayam Ras',        'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Daging-dagingan'],
            ['kode' => '003', 'nama' => 'Daging Sapi',            'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Daging-dagingan'],
            ['kode' => '004', 'nama' => 'Ikan Cakalang/Tongkol',  'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Ikan Segar'],
            ['kode' => '005', 'nama' => 'Telur Ayam Ras',         'satuan' => 'butir', 'kelompok' => 'Makanan', 'subkelompok' => 'Telur & Susu'],
            ['kode' => '006', 'nama' => 'Cabai Merah',            'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Bumbu-bumbuan'],
            ['kode' => '007', 'nama' => 'Cabai Rawit',            'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Bumbu-bumbuan'],
            ['kode' => '008', 'nama' => 'Bawang Merah',           'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Bumbu-bumbuan'],
            ['kode' => '009', 'nama' => 'Bawang Putih',           'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Bumbu-bumbuan'],
            ['kode' => '010', 'nama' => 'Tomat',                  'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Sayuran'],
            ['kode' => '011', 'nama' => 'Minyak Goreng',          'satuan' => 'liter', 'kelompok' => 'Makanan', 'subkelompok' => 'Lemak & Minyak'],
            ['kode' => '012', 'nama' => 'Gula Pasir',             'satuan' => 'kg',    'kelompok' => 'Makanan', 'subkelompok' => 'Gula & Kembang Gula'],
            // Non-Makanan
            ['kode' => '101', 'nama' => 'Bensin',                 'satuan' => 'liter', 'kelompok' => 'Non-Makanan', 'subkelompok' => 'Transportasi'],
            ['kode' => '102', 'nama' => 'Solar',                  'satuan' => 'liter', 'kelompok' => 'Non-Makanan', 'subkelompok' => 'Transportasi'],
            ['kode' => '103', 'nama' => 'Tarif Angkutan Kota',    'satuan' => 'trip',  'kelompok' => 'Non-Makanan', 'subkelompok' => 'Transportasi'],
            ['kode' => '104', 'nama' => 'Sewa Rumah',             'satuan' => 'bulan', 'kelompok' => 'Non-Makanan', 'subkelompok' => 'Perumahan'],
            ['kode' => '105', 'nama' => 'Tarif Listrik',          'satuan' => 'kWh',   'kelompok' => 'Non-Makanan', 'subkelompok' => 'Perumahan'],
            ['kode' => '106', 'nama' => 'LPG 3kg',                'satuan' => 'tabung','kelompok' => 'Non-Makanan', 'subkelompok' => 'Perumahan'],
        ];

        foreach ($masterKomoditas as $k) {
            Komoditas::firstOrCreate(
                ['kode_komoditas' => $k['kode']],
                [
                    'nama_komoditas' => $k['nama'],
                    'satuan'         => $k['satuan'],
                    'kelompok'       => $k['kelompok'],
                    'subkelompok'    => $k['subkelompok'],
                    'is_active'      => true,
                ]
            );
        }

        // 6. Aktifkan semua komoditas di semua wilayah Kab/Kota (default)
        $semuaKomoditas = Komoditas::all();
        $semuaWilayahKabkota = Wilayah::whereIn('tipe', ['kabupaten', 'kota'])->get();

        foreach ($semuaWilayahKabkota as $wil) {
            foreach ($semuaKomoditas as $kom) {
                KomoditasWilayah::firstOrCreate(
                    ['komoditas_id' => $kom->id, 'wilayah_id' => $wil->id],
                    ['status' => 'aktif']
                );
            }
        }

        // 7. Buat 3 Periode (Mei, Juni, Juli 2026)
        $periodes = [];
        $periodeConfigs = [
            ['bulan' => 5, 'tahun' => 2026, 'status' => 'ditutup', 'keterangan' => 'Periode rekonsiliasi Mei 2026'],
            ['bulan' => 6, 'tahun' => 2026, 'status' => 'ditutup', 'keterangan' => 'Periode rekonsiliasi Juni 2026'],
            ['bulan' => 7, 'tahun' => 2026, 'status' => 'aktif',   'keterangan' => 'Periode rekonsiliasi Juli 2026'],
        ];

        foreach ($periodeConfigs as $pc) {
            $tanggalBuka  = sprintf('%d-%02d-01', $pc['tahun'], $pc['bulan']);
            $tanggalTutup = date('Y-m-t', strtotime($tanggalBuka));

            $periodes[] = Periode::firstOrCreate(
                ['bulan' => $pc['bulan'], 'tahun' => $pc['tahun']],
                [
                    'tanggal_buka'  => $tanggalBuka,
                    'tanggal_tutup' => $tanggalTutup,
                    'status'        => $pc['status'],
                    'keterangan'    => $pc['keterangan'],
                ]
            );
        }

        // =====================================================================
        //  8. SEED DATA HARGA — 3 periode × 15 wilayah × 18 komoditas
        // =====================================================================
        echo "\n📊 Generating Data Harga...\n";

        // Harga dasar realistis per komoditas (Rupiah)
        $hargaDasar = [
            '001' => 14500,  // Beras per kg
            '002' => 38000,  // Daging Ayam Ras per kg
            '003' => 135000, // Daging Sapi per kg
            '004' => 35000,  // Ikan Cakalang per kg
            '005' => 2800,   // Telur Ayam per butir
            '006' => 45000,  // Cabai Merah per kg
            '007' => 55000,  // Cabai Rawit per kg
            '008' => 40000,  // Bawang Merah per kg
            '009' => 42000,  // Bawang Putih per kg
            '010' => 12000,  // Tomat per kg
            '011' => 18500,  // Minyak Goreng per liter
            '012' => 17500,  // Gula Pasir per kg
            '101' => 10000,  // Bensin per liter
            '102' => 6800,   // Solar per liter
            '103' => 5000,   // Tarif Angkot per trip
            '104' => 1500000,// Sewa Rumah per bulan
            '105' => 1467,   // Tarif Listrik per kWh
            '106' => 23000,  // LPG 3kg per tabung
        ];

        // Komoditas volatil — cabai, bawang, ikan, tomat, telur
        $volatilKodes = ['004', '005', '006', '007', '008', '009', '010'];
        // Komoditas stabil — BBM, listrik, sewa rumah
        $stabilKodes  = ['101', '102', '103', '104', '105', '106'];

        $dataHargaRecords = [];
        $komoditasMap = Komoditas::all()->keyBy('kode_komoditas');
        $wilayahKabKotaList = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();

        foreach ($periodes as $periodeIdx => $periode) {
            foreach ($wilayahKabKotaList as $wilayah) {
                foreach ($semuaKomoditas as $komoditas) {
                    $kode = $komoditas->kode_komoditas;
                    $base = $hargaDasar[$kode] ?? 10000;

                    // Seed hash for deterministic but varied data
                    $seed = crc32($kode . $wilayah->kode_wilayah . $periodeIdx);
                    mt_srand($seed);

                    // Variasi harga per wilayah (±5% dari harga dasar)
                    $wilayahFactor = 1 + ((mt_rand(-50, 50)) / 1000);

                    // Variasi per periode (simulasi tren)
                    $periodeFactor = 1 + (($periodeIdx - 1) * 0.005); // slight upward trend

                    $harga = round($base * $wilayahFactor * $periodeFactor, 2);

                    // Inflasi MtM — volatil vs stabil
                    if (in_array($kode, $volatilKodes)) {
                        $mtm = round((mt_rand(-800, 800)) / 100, 4); // -8% to +8%
                    } elseif (in_array($kode, $stabilKodes)) {
                        $mtm = round((mt_rand(-20, 20)) / 100, 4);   // -0.2% to +0.2%
                    } else {
                        $mtm = round((mt_rand(-300, 300)) / 100, 4); // -3% to +3%
                    }

                    // Derive YtD and YoY from MtM with some additional variation
                    $ytd = round($mtm * ($periodeIdx + 3) + (mt_rand(-100, 100) / 100), 4);
                    $yoy = round($mtm * 8 + (mt_rand(-200, 200) / 100), 4);

                    // Andil — fraction of MtM weighted by importance
                    $andilMtm = round($mtm * (mt_rand(1, 15) / 100), 4);
                    $andilYtd = round($ytd * (mt_rand(1, 10) / 100), 4);
                    $andilYoy = round($yoy * (mt_rand(1, 10) / 100), 4);

                    $dataHargaRecords[] = [
                        'periode_id'   => $periode->id,
                        'wilayah_id'   => $wilayah->id,
                        'komoditas_id' => $komoditas->id,
                        'tipe_indeks'  => 'IHK',
                        'harga_level'  => $harga,
                        'inflasi_mtm'  => $mtm,
                        'inflasi_ytd'  => $ytd,
                        'inflasi_yoy'  => $yoy,
                        'andil_mtm'    => $andilMtm,
                        'andil_ytd'    => $andilYtd,
                        'andil_yoy'    => $andilYoy,
                        'uploaded_by'  => $userProvinsi->id,
                        'sumber_file'  => 'seeder',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
            }
        }

        // Bulk insert in chunks for performance
        foreach (array_chunk($dataHargaRecords, 200) as $chunk) {
            DataHarga::insert($chunk);
        }
        echo "   ✅ " . count($dataHargaRecords) . " record Data Harga\n";

        // =====================================================================
        //  9. SEED ALASAN PERUBAHAN — untuk komoditas signifikan (>1% MtM)
        // =====================================================================
        echo "\n📝 Generating Alasan Perubahan...\n";

        // Template alasan realistis
        $alasanTemplates = [
            'naik' => [
                'Harga {komoditas} mengalami kenaikan signifikan karena pasokan yang menurun di pasar tradisional.',
                'Permintaan {komoditas} meningkat tajam menjelang hari raya, menyebabkan kenaikan harga.',
                'Gangguan distribusi dari daerah produsen menyebabkan kelangkaan {komoditas} di pasar lokal.',
                'Cuaca buruk di daerah sentra produksi mengakibatkan penurunan pasokan {komoditas}.',
                'Kenaikan harga BBM berdampak pada biaya transportasi yang meningkatkan harga {komoditas} di tingkat konsumen.',
                'Stok {komoditas} di gudang distributor menipis sehingga harga pasar mengalami kenaikan.',
            ],
            'turun' => [
                'Pasokan {komoditas} dari daerah produsen melimpah sehingga harga mengalami penurunan.',
                'Panen raya {komoditas} di wilayah sekitar menyebabkan harga turun signifikan.',
                'Permintaan {komoditas} menurun pasca hari raya, menyebabkan harga kembali normal.',
                'Masuknya {komoditas} impor dengan harga lebih rendah menekan harga pasar lokal.',
                'Perbaikan jalur distribusi memperlancar pasokan {komoditas} dan menurunkan harga.',
                'Produksi lokal {komoditas} meningkat seiring membaiknya kondisi cuaca.',
            ],
        ];

        $rekomendasiTemplates = [
            'Perlu monitoring ketat terhadap pasokan dan distribusi di minggu-minggu mendatang.',
            'Disarankan koordinasi dengan dinas perdagangan untuk menstabilkan harga.',
            'Diperlukan penambahan stok cadangan pemerintah untuk mengantisipasi kelangkaan.',
            'Evaluasi rantai pasok perlu dilakukan untuk mencegah fluktuasi serupa.',
            'Disarankan kerja sama antar wilayah untuk menjamin ketersediaan pasokan.',
            null, // sometimes no rekomendasi
        ];

        $faktorOptions = array_keys(AlasanPerubahan::$faktors);
        $statusOptions = ['submitted', 'submitted', 'submitted', 'disetujui', 'disetujui', 'revisi', 'draft'];

        $alasanRecords = [];
        $periodeAktif = $periodes[2]; // Juli 2026

        // Generate alasan for signifikan komoditas across wilayah for the active periode
        $significantData = DataHarga::where('periode_id', $periodeAktif->id)
            ->where(function ($q) {
                $q->where('inflasi_mtm', '>=', 1.0)
                  ->orWhere('inflasi_mtm', '<=', -1.0);
            })
            ->get();

        $alasanCount = 0;
        foreach ($significantData as $dh) {
            $komoditas = $komoditasMap->get(
                Komoditas::find($dh->komoditas_id)?->kode_komoditas
            );
            if (!$komoditas) continue;

            $namaKom = $komoditas->nama_komoditas;
            $isNaik = (float) $dh->inflasi_mtm > 0;

            $seed = crc32($dh->wilayah_id . $dh->komoditas_id . 'alasan');
            mt_srand($seed);

            $status = $statusOptions[mt_rand(0, count($statusOptions) - 1)];
            $templates = $isNaik ? $alasanTemplates['naik'] : $alasanTemplates['turun'];
            $alasanText = str_replace('{komoditas}', $namaKom, $templates[mt_rand(0, count($templates) - 1)]);
            $rekomendasi = $rekomendasiTemplates[mt_rand(0, count($rekomendasiTemplates) - 1)];

            // Pick 1-3 random faktor pendorong
            $numFaktors = mt_rand(1, 3);
            $shuffledFaktors = $faktorOptions;
            shuffle($shuffledFaktors);
            $selectedFaktors = array_slice($shuffledFaktors, 0, $numFaktors);

            // Find the user for this wilayah
            $wilayahModel = Wilayah::find($dh->wilayah_id);
            $wilayahUser = User::where('wilayah_id', $dh->wilayah_id)->first();

            $submittedAt = $status !== 'draft' ? now()->subDays(mt_rand(1, 15)) : null;
            $reviewedAt  = in_array($status, ['disetujui', 'revisi']) ? now()->subDays(mt_rand(0, 5)) : null;
            $reviewedBy  = $reviewedAt ? $userProvinsi->id : null;

            $catatanProvinsi = null;
            if ($status === 'revisi') {
                $catatanProvinsi = 'Mohon dilengkapi dengan data pendukung dari pasar tradisional. Alasan masih terlalu umum.';
            } elseif ($status === 'disetujui') {
                $catatanProvinsi = mt_rand(0, 1) ? 'Alasan sesuai dengan hasil pemantauan lapangan.' : null;
            }

            $alasanRecords[] = [
                'periode_id'       => $periodeAktif->id,
                'wilayah_id'       => $dh->wilayah_id,
                'komoditas_id'     => $dh->komoditas_id,
                'alasan'           => $alasanText,
                'faktor_pendorong' => json_encode($selectedFaktors),
                'rekomendasi'      => $rekomendasi,
                'status'           => $status,
                'submitted_by'     => $wilayahUser?->id,
                'submitted_at'     => $submittedAt,
                'reviewed_by'      => $reviewedBy,
                'reviewed_at'      => $reviewedAt,
                'catatan_provinsi' => $catatanProvinsi,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
            $alasanCount++;
        }

        // Also add alasan for periode ditutup (Juni 2026) — all disetujui
        $periodeLalu = $periodes[1]; // Juni 2026
        $significantDataLalu = DataHarga::where('periode_id', $periodeLalu->id)
            ->where(function ($q) {
                $q->where('inflasi_mtm', '>=', 1.5)
                  ->orWhere('inflasi_mtm', '<=', -1.5);
            })
            ->limit(50)
            ->get();

        foreach ($significantDataLalu as $dh) {
            $komoditas = Komoditas::find($dh->komoditas_id);
            if (!$komoditas) continue;

            $namaKom = $komoditas->nama_komoditas;
            $isNaik = (float) $dh->inflasi_mtm > 0;

            $seed = crc32($dh->wilayah_id . $dh->komoditas_id . 'alasan_lalu');
            mt_srand($seed);

            $templates = $isNaik ? $alasanTemplates['naik'] : $alasanTemplates['turun'];
            $alasanText = str_replace('{komoditas}', $namaKom, $templates[mt_rand(0, count($templates) - 1)]);
            $rekomendasi = $rekomendasiTemplates[mt_rand(0, count($rekomendasiTemplates) - 1)];

            $numFaktors = mt_rand(1, 3);
            $shuffledFaktors = $faktorOptions;
            shuffle($shuffledFaktors);
            $selectedFaktors = array_slice($shuffledFaktors, 0, $numFaktors);

            $wilayahUser = User::where('wilayah_id', $dh->wilayah_id)->first();

            $alasanRecords[] = [
                'periode_id'       => $periodeLalu->id,
                'wilayah_id'       => $dh->wilayah_id,
                'komoditas_id'     => $dh->komoditas_id,
                'alasan'           => $alasanText,
                'faktor_pendorong' => json_encode($selectedFaktors),
                'rekomendasi'      => $rekomendasi,
                'status'           => 'disetujui',
                'submitted_by'     => $wilayahUser?->id,
                'submitted_at'     => now()->subMonth()->subDays(mt_rand(5, 20)),
                'reviewed_by'      => $userProvinsi->id,
                'reviewed_at'      => now()->subMonth()->subDays(mt_rand(1, 5)),
                'catatan_provinsi' => mt_rand(0, 1) ? 'Alasan diterima, sesuai kondisi lapangan.' : null,
                'created_at'       => now()->subMonth(),
                'updated_at'       => now()->subMonth(),
            ];
            $alasanCount++;
        }

        foreach (array_chunk($alasanRecords, 100) as $chunk) {
            AlasanPerubahan::insert($chunk);
        }
        echo "   ✅ " . $alasanCount . " record Alasan Perubahan\n";

        // =====================================================================
        //  10. SEED NOTIFIKASI
        // =====================================================================
        echo "\n🔔 Generating Notifikasi...\n";

        $notifRecords = [];

        // Notif: Periode Juli 2026 dibuka — ke semua user kab/kota
        foreach ($usersByWilayahKode as $kode => $kabUser) {
            $notifRecords[] = [
                'user_id'        => $kabUser->id,
                'judul'          => 'Periode Rekonsiliasi Juli 2026 Dibuka',
                'pesan'          => 'Periode rekonsiliasi untuk bulan Juli 2026 telah dibuka. Silakan input alasan perubahan harga untuk komoditas yang mengalami perubahan signifikan.',
                'tipe'           => 'periode_buka',
                'referensi_id'   => $periodeAktif->id,
                'referensi_tipe' => 'App\Models\Periode',
                'url'            => '/wilayah/input-alasan',
                'is_read'        => mt_rand(0, 1) ? true : false,
                'read_at'        => mt_rand(0, 1) ? now()->subDays(mt_rand(1, 10)) : null,
                'created_at'     => now()->subDays(15),
                'updated_at'     => now()->subDays(15),
            ];
        }

        // Notif: Input alasan — per komoditas signifikan (first 5 wilayah only to keep manageable)
        $firstFiveWilayah = array_slice(array_values($usersByWilayahKode), 0, 5);
        foreach ($firstFiveWilayah as $kabUser) {
            $notifRecords[] = [
                'user_id'        => $kabUser->id,
                'judul'          => 'Input Alasan: Cabai Rawit naik 5.2%',
                'pesan'          => 'Harga Cabai Rawit di wilayah Anda mengalami kenaikan 5.2% pada periode Juli 2026. Mohon segera input alasan perubahan harga.',
                'tipe'           => 'input_alasan',
                'referensi_id'   => null,
                'referensi_tipe' => null,
                'url'            => '/wilayah/input-alasan',
                'is_read'        => false,
                'read_at'        => null,
                'created_at'     => now()->subDays(10),
                'updated_at'     => now()->subDays(10),
            ];

            $notifRecords[] = [
                'user_id'        => $kabUser->id,
                'judul'          => 'Input Alasan: Bawang Merah turun 3.1%',
                'pesan'          => 'Harga Bawang Merah di wilayah Anda mengalami penurunan 3.1% pada periode Juli 2026. Mohon segera input alasan perubahan harga.',
                'tipe'           => 'input_alasan',
                'referensi_id'   => null,
                'referensi_tipe' => null,
                'url'            => '/wilayah/input-alasan',
                'is_read'        => false,
                'read_at'        => null,
                'created_at'     => now()->subDays(8),
                'updated_at'     => now()->subDays(8),
            ];
        }

        // Notif: Revisi alasan (for wilayah that have 'revisi' status)
        $revisiAlasans = AlasanPerubahan::where('status', 'revisi')
            ->where('periode_id', $periodeAktif->id)
            ->with('komoditas', 'wilayah')
            ->limit(10)
            ->get();

        foreach ($revisiAlasans as $ra) {
            $wilayahUser = User::where('wilayah_id', $ra->wilayah_id)->first();
            if (!$wilayahUser) continue;

            $notifRecords[] = [
                'user_id'        => $wilayahUser->id,
                'judul'          => 'Perlu Revisi: ' . ($ra->komoditas->nama_komoditas ?? 'Komoditas'),
                'pesan'          => 'Alasan perubahan harga ' . ($ra->komoditas->nama_komoditas ?? '-') . ' memerlukan revisi. Catatan provinsi: "Mohon dilengkapi dengan data pendukung."',
                'tipe'           => 'revisi_alasan',
                'referensi_id'   => $ra->id,
                'referensi_tipe' => 'App\Models\AlasanPerubahan',
                'url'            => '/wilayah/input-alasan/' . $ra->id,
                'is_read'        => false,
                'read_at'        => null,
                'created_at'     => now()->subDays(3),
                'updated_at'     => now()->subDays(3),
            ];
        }

        // Notif: Reminder deadline untuk yang belum lengkap
        $reminderTargets = array_slice(array_values($usersByWilayahKode), 5, 5);
        foreach ($reminderTargets as $kabUser) {
            $notifRecords[] = [
                'user_id'        => $kabUser->id,
                'judul'          => 'Reminder: Deadline Input Alasan H-3',
                'pesan'          => 'Deadline input alasan perubahan harga untuk periode Juli 2026 tinggal 3 hari lagi. Anda masih memiliki beberapa komoditas yang belum diisi.',
                'tipe'           => 'reminder_deadline',
                'referensi_id'   => $periodeAktif->id,
                'referensi_tipe' => 'App\Models\Periode',
                'url'            => '/wilayah/input-alasan',
                'is_read'        => false,
                'read_at'        => null,
                'created_at'     => now()->subDays(1),
                'updated_at'     => now()->subDays(1),
            ];
        }

        // Notif: ke Provinsi — wilayah sudah submit
        $notifRecords[] = [
            'user_id'        => $userProvinsi->id,
            'judul'          => 'Manado telah submit alasan',
            'pesan'          => 'BPS Kota Manado telah mengirimkan alasan perubahan harga untuk 8 komoditas pada periode Juli 2026.',
            'tipe'           => 'input_alasan',
            'referensi_id'   => null,
            'referensi_tipe' => null,
            'url'            => '/provinsi/alasan',
            'is_read'        => false,
            'read_at'        => null,
            'created_at'     => now()->subDays(5),
            'updated_at'     => now()->subDays(5),
        ];

        $notifRecords[] = [
            'user_id'        => $userProvinsi->id,
            'judul'          => 'Bitung telah submit alasan',
            'pesan'          => 'BPS Kota Bitung telah mengirimkan alasan perubahan harga untuk 6 komoditas pada periode Juli 2026.',
            'tipe'           => 'input_alasan',
            'referensi_id'   => null,
            'referensi_tipe' => null,
            'url'            => '/provinsi/alasan',
            'is_read'        => true,
            'read_at'        => now()->subDays(2),
            'created_at'     => now()->subDays(7),
            'updated_at'     => now()->subDays(7),
        ];

        Notifikasi::insert($notifRecords);
        echo "   ✅ " . count($notifRecords) . " record Notifikasi\n";

        // Reset random seed
        mt_srand();

        // =====================================================================
        //  SUMMARY
        // =====================================================================
        echo "\n" . str_repeat('=', 60) . "\n";
        echo "✅ Seeder selesai! Database berhasil diisi.\n";
        echo str_repeat('=', 60) . "\n";
        echo "   📍 1 Provinsi + 15 Kabupaten/Kota\n";
        echo "   👤 16 Users (1 provinsi + 15 kab/kota)\n";
        echo "   📦 " . count($masterKomoditas) . " Komoditas master\n";
        echo "   📅 3 Periode (Mei, Juni, Juli 2026)\n";
        echo "   📊 " . count($dataHargaRecords) . " record Data Harga\n";
        echo "   📝 " . $alasanCount . " record Alasan Perubahan\n";
        echo "   🔔 " . count($notifRecords) . " record Notifikasi\n";
        echo "\nCredentials login:\n";
        echo "  Provinsi   : provinsi@bps-sulut.go.id / password\n";
        echo "  Kab/Kota   : manado@bps-sulut.go.id / password (contoh)\n";
        echo "  Semua akun : [kode_wilayah]@bps-sulut.go.id / password\n";
    }
}

<?php

namespace Database\Seeders;

use App\Models\Komoditas;
use App\Models\KomoditasWilayah;
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

        // 7. Buat periode aktif (bulan ini)
        Periode::firstOrCreate(
            ['bulan' => now()->month, 'tahun' => now()->year],
            [
                'tanggal_buka'  => now()->startOfMonth()->toDateString(),
                'tanggal_tutup' => now()->endOfMonth()->toDateString(),
                'status'        => 'aktif',
                'keterangan'    => 'Periode rekonsiliasi ' . now()->format('F Y'),
            ]
        );

        echo "✅ Seeder selesai! Database berhasil diisi.\n";
        echo "   - 1 Provinsi + 15 Kabupaten/Kota\n";
        echo "   - 16 Users (1 provinsi + 15 kab/kota)\n";
        echo "   - " . count($masterKomoditas) . " Komoditas master\n";
        echo "   - 1 Periode aktif (" . now()->format('F Y') . ")\n";
        echo "\nCredentials login:\n";
        echo "  Provinsi: provinsi@bps-sulut.go.id / password\n";
        echo "  Kab/Kota: manado@bps-sulut.go.id / password (contoh)\n";
    }
}

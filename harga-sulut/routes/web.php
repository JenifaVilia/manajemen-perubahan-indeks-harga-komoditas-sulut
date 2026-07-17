<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Provinsi\DashboardProvinsiController;
use App\Http\Controllers\Provinsi\DataHargaController;
use App\Http\Controllers\Provinsi\AlasanMonitorController;
use App\Http\Controllers\Provinsi\VisualisasiController;
use App\Http\Controllers\Provinsi\ManajemenPeriodeController;
use App\Http\Controllers\Provinsi\ManajemenKomoditasController;
use App\Http\Controllers\Provinsi\ManajemenUserController;
use App\Http\Controllers\Provinsi\PermintaanKomoditasController;
use App\Http\Controllers\KabupatenKota\DashboardWilayahController;
use App\Http\Controllers\KabupatenKota\InputAlasanController;
use App\Http\Controllers\KabupatenKota\HistoriHargaController;
use App\Http\Controllers\KabupatenKota\KomoditasWilayahController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\EksporController;
use Illuminate\Support\Facades\Route;

// ===========================================================================
// AUTH ROUTES
// ===========================================================================
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Redirect root to appropriate dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isProvinsi()
            ? redirect()->route('provinsi.dashboard')
            : redirect()->route('wilayah.dashboard');
    }
    return redirect()->route('login');
});

// ===========================================================================
// PROVINSI ROUTES
// ===========================================================================
Route::middleware(['auth', 'role:provinsi'])->prefix('provinsi')->name('provinsi.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardProvinsiController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/peta-data', [DashboardProvinsiController::class, 'petaData'])->name('dashboard.peta-data');
    Route::get('/dashboard/chart-inflasi', [DashboardProvinsiController::class, 'chartInflasi'])->name('dashboard.chart-inflasi');

    // Data Harga
    Route::prefix('data-harga')->name('data-harga.')->group(function () {
        Route::get('/',         [DataHargaController::class, 'index'])->name('index');
        Route::get('/upload',   [DataHargaController::class, 'formUpload'])->name('upload');
        Route::post('/upload',  [DataHargaController::class, 'prosesUpload'])->name('upload.proses');
        Route::get('/manual',   [DataHargaController::class, 'formManual'])->name('manual');
        Route::post('/manual',  [DataHargaController::class, 'simpanManual'])->name('manual.simpan');
        Route::get('/riwayat',  [DataHargaController::class, 'riwayat'])->name('riwayat');
        Route::get('/template', [DataHargaController::class, 'downloadTemplate'])->name('template');
    });

    // Monitor Alasan
    Route::prefix('alasan')->name('alasan.')->group(function () {
        Route::get('/',                          [AlasanMonitorController::class, 'index'])->name('index');
        Route::get('/{alasan}',                  [AlasanMonitorController::class, 'show'])->name('show');
        Route::post('/{alasan}/setujui',         [AlasanMonitorController::class, 'setujui'])->name('setujui');
        Route::post('/{alasan}/minta-revisi',    [AlasanMonitorController::class, 'mintaRevisi'])->name('minta-revisi');
    });

    // Visualisasi
    Route::prefix('visualisasi')->name('visualisasi.')->group(function () {
        Route::get('/tabel-relatif',    [VisualisasiController::class, 'tabelRelatif'])->name('tabel-relatif');
        Route::get('/output-mtm',       [VisualisasiController::class, 'outputMtm'])->name('output-mtm');
        Route::get('/tren-komoditas',   [VisualisasiController::class, 'trenKomoditas'])->name('tren-komoditas');
        Route::get('/data-tabel',       [VisualisasiController::class, 'dataTabel'])->name('data-tabel'); // JSON for tables
    });

    // Manajemen Periode
    Route::resource('periode', ManajemenPeriodeController::class)->except(['show']);
    Route::post('/periode/{periode}/buka',  [ManajemenPeriodeController::class, 'buka'])->name('periode.buka');
    Route::post('/periode/{periode}/tutup', [ManajemenPeriodeController::class, 'tutup'])->name('periode.tutup');

    // Manajemen Komoditas
    Route::resource('komoditas', ManajemenKomoditasController::class)->except(['show']);

    // Manajemen User
    Route::resource('users', ManajemenUserController::class)->except(['show']);
    Route::post('/users/{user}/reset-password', [ManajemenUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/toggle-aktif',   [ManajemenUserController::class, 'toggleAktif'])->name('users.toggle-aktif');

    // Permintaan Komoditas
    Route::prefix('permintaan-komoditas')->name('permintaan-komoditas.')->group(function () {
        Route::get('/',                     [PermintaanKomoditasController::class, 'index'])->name('index');
        Route::post('/{kw}/approve',        [PermintaanKomoditasController::class, 'approve'])->name('approve');
        Route::post('/{kw}/reject',         [PermintaanKomoditasController::class, 'reject'])->name('reject');
    });

    // Ekspor
    Route::prefix('ekspor')->name('ekspor.')->group(function () {
        Route::get('/alasan',         [EksporController::class, 'alasanProvinsi'])->name('alasan');
        Route::get('/tabel-relatif',  [EksporController::class, 'tabelRelatif'])->name('tabel-relatif');
        Route::get('/rekap-periode',  [EksporController::class, 'rekapPeriode'])->name('rekap-periode');
    });
});

// ===========================================================================
// KABUPATEN/KOTA ROUTES
// ===========================================================================
Route::middleware(['auth', 'role:kabupaten_kota'])->prefix('wilayah')->name('wilayah.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardWilayahController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardWilayahController::class, 'chartData'])->name('dashboard.chart-data');

    // Input Alasan
    Route::prefix('input-alasan')->name('input-alasan.')->group(function () {
        Route::get('/',             [InputAlasanController::class, 'index'])->name('index');
        Route::get('/{alasan}',     [InputAlasanController::class, 'show'])->name('show');
        Route::post('/store',       [InputAlasanController::class, 'store'])->name('store');
        Route::put('/{alasan}',     [InputAlasanController::class, 'update'])->name('update');
        Route::post('/{alasan}/submit', [InputAlasanController::class, 'submit'])->name('submit');
    });

    // Histori Harga
    Route::get('/histori',              [HistoriHargaController::class, 'index'])->name('histori.index');
    Route::get('/histori/chart-data',   [HistoriHargaController::class, 'chartData'])->name('histori.chart-data');
    Route::get('/histori/ekspor',       [EksporController::class, 'historiWilayah'])->name('histori.ekspor');

    // Komoditas Wilayah
    Route::prefix('komoditas')->name('komoditas.')->group(function () {
        Route::get('/',                 [KomoditasWilayahController::class, 'index'])->name('index');
        Route::post('/ajukan-tambah',   [KomoditasWilayahController::class, 'ajukanTambah'])->name('ajukan-tambah');
        Route::post('/{kw}/ajukan-hapus', [KomoditasWilayahController::class, 'ajukanHapus'])->name('ajukan-hapus');
        Route::get('/permintaan',       [KomoditasWilayahController::class, 'daftarPermintaan'])->name('permintaan');
    });

    // Ekspor wilayah sendiri
    Route::get('/ekspor/alasan', [EksporController::class, 'alasanWilayah'])->name('ekspor.alasan');
});

// ===========================================================================
// SHARED ROUTES (Provinsi & Kab/Kota)
// ===========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/notifikasi',              [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/baca',   [NotifikasiController::class, 'markRead'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua',  [NotifikasiController::class, 'markAllRead'])->name('notifikasi.baca-semua');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'unreadCount'])->name('notifikasi.unread-count');
});

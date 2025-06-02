<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KepalaSekolahController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;

Route::get('/test-middleware-class', function () {
    if (class_exists(\App\Http\Middleware\RoleMiddleware::class)) {
        return "SUCCESS: Kelas App\Http\Middleware\RoleMiddleware DITEMUKAN.";
    } else {
        return "ERROR: Kelas App\Http\Middleware\RoleMiddleware TIDAK DITEMUKAN.";
    }
});

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes

// Admin Routes
Route::middleware(['auth', 'can:is-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Pegawai Management
    Route::get('/pegawai', [AdminController::class, 'pegawaiIndex'])->name('pegawai');
    Route::get('/pegawai/create', [AdminController::class, 'pegawaiCreate'])->name('pegawai.create');
    Route::post('/pegawai', [AdminController::class, 'pegawaiStore'])->name('pegawai.store');
    Route::get('/pegawai/{pegawai}/edit', [AdminController::class, 'pegawaiEdit'])->name('pegawai.edit');
    Route::put('/pegawai/{pegawai}', [AdminController::class, 'pegawaiUpdate'])->name('pegawai.update');
    Route::delete('/pegawai/{pegawai}', [AdminController::class, 'pegawaiDestroy'])->name('pegawai.destroy');

    // Periode Management
    Route::get('/periode', [AdminController::class, 'periodeIndex'])->name('periode');
    Route::get('/periode/create', [AdminController::class, 'periodeCreate'])->name('periode.create');
    Route::post('/periode', [AdminController::class, 'periodeStore'])->name('periode.store');
    Route::get('/periode/{periode}/edit', [AdminController::class, 'periodeEdit'])->name('periode.edit');
    Route::put('/periode/{periode}', [AdminController::class, 'periodeUpdate'])->name('periode.update');
    Route::delete('/periode/{periode}', [AdminController::class, 'periodeDestroy'])->name('periode.destroy');
    Route::put('/periode/{id}/activate', [AdminController::class, 'periodeActivate'])->name('periode.activate');

    // Jabatan Management
    Route::get('/jabatan', [AdminController::class, 'jabatanIndex'])->name('jabatan');
    Route::get('/jabatan/create', [AdminController::class, 'jabatanCreate'])->name('jabatan.create');
    Route::post('/jabatan', [AdminController::class, 'jabatanStore'])->name('jabatan.store');
    Route::get('/jabatan/{jabatan}/edit', [AdminController::class, 'jabatanEdit'])->name('jabatan.edit');
    Route::put('/jabatan/{jabatan}', [AdminController::class, 'jabatanUpdate'])->name('jabatan.update');
    Route::delete('/jabatan/{jabatan}', [AdminController::class, 'jabatanDestroy'])->name('jabatan.destroy');

    // Reports
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/export', [AdminController::class, 'exportLaporan'])->name('laporan.export');

    // Additional admin routes
    // Sebaiknya hapus resource controller ini jika sudah didefinisikan secara individual di atas untuk menghindari duplikasi
    // Route::resource('jabatan', AdminController::class . '@jabatan');
    // Route::resource('pegawai', AdminController::class . '@pegawai');
});

// Kepala Sekolah Routes
Route::middleware(['auth', 'can:is-kepala-sekolah'])->prefix('kepala')->name('kepala.')->group(function () {
    Route::get('/dashboard', [KepalaSekolahController::class, 'dashboard'])->name('dashboard');

    // Persetujuan SKP
    Route::get('/persetujuan', [KepalaSekolahController::class, 'persetujuan'])->name('persetujuan');
    Route::get('/persetujuan/{id}', [KepalaSekolahController::class, 'persetujuanDetail'])->name('persetujuan.detail');
    Route::post('/persetujuan/{id}/approve', [KepalaSekolahController::class, 'approve'])->name('persetujuan.approve');
    Route::post('/persetujuan/{id}/reject', [KepalaSekolahController::class, 'reject'])->name('persetujuan.reject');

    // Penilaian SKP
    Route::get('/penilaian-skp', [KepalaSekolahController::class, 'penilaianSkpIndex'])->name('penilaian-skp.index');
    Route::get('/penilaian-skp/{id}/nilai', [KepalaSekolahController::class, 'penilaianSkpCreate'])->name('penilaian-skp.create');
    Route::post('/penilaian-skp/{id}', [KepalaSekolahController::class, 'penilaianSkpStore'])->name('penilaian-skp.store');
    Route::get('/penilaian-skp/bukti-dukung/{filename}', [KepalaSekolahController::class, 'showBuktiDukungPenilaian'])->name('penilaian-skp.bukti_dukung');

    // Monitoring
    Route::get('/monitoring', [KepalaSekolahController::class, 'monitoring'])->name('monitoring');

    // Reports
    Route::get('/laporan', [KepalaSekolahController::class, 'laporan'])->name('laporan');
});

// Pegawai Routes
Route::middleware(['auth', 'can:is-pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [PegawaiController::class, 'dashboard'])->name('dashboard');

    // Sasaran Kerja
    Route::get('/sasaran', [PegawaiController::class, 'sasaranIndex'])->name('sasaran');
    Route::get('/sasaran/create', [PegawaiController::class, 'sasaranCreate'])->name('sasaran.create');
    Route::post('/sasaran', [PegawaiController::class, 'sasaranStore'])->name('sasaran.store');
    Route::get('/sasaran/{sasaran}/edit', [PegawaiController::class, 'sasaranEdit'])->name('sasaran.edit');
    Route::put('/sasaran/{sasaran}', [PegawaiController::class, 'sasaranUpdate'])->name('sasaran.update');
    Route::get('/sasaran/{sasaran}', [PegawaiController::class, 'sasaranShow'])->name('sasaran.show');

    // Realisasi Kerja
    Route::get('/realisasi', [PegawaiController::class, 'realisasiIndex'])->name('realisasi');
    Route::get('/realisasi/create', [PegawaiController::class, 'realisasiCreate'])->name('realisasi.create');
    Route::post('/realisasi', [PegawaiController::class, 'realisasiStore'])->name('realisasi.store');
    Route::get('/realisasi/{realisasi}/edit', [PegawaiController::class, 'realisasiEdit'])->name('realisasi.edit');
    Route::put('/realisasi/{realisasi}', [PegawaiController::class, 'realisasiUpdate'])->name('realisasi.update');
    Route::delete('/realisasi/{realisasi}', [PegawaiController::class, 'realisasiDestroy'])->name('realisasi.destroy');
    Route::get('/realisasi/bukti-dukung/{filename}', [PegawaiController::class, 'showBuktiDukungRealisasi'])->name('realisasi.bukti_dukung');

    // Penilaian
    Route::get('/penilaian', [PegawaiController::class, 'penilaian'])->name('penilaian');

    // Rencana Tindak Lanjut
    Route::get('/rencana', [PegawaiController::class, 'rencanaIndex'])->name('rencana');
    Route::post('/rencana', [PegawaiController::class, 'rencanaStore'])->name('rencana.store');
});

// Export routes
Route::middleware(['auth'])->prefix('export')->name('export.')->group(function () {
    Route::get('/laporan', [ExportController::class, 'exportLaporan'])->name('laporan');
    Route::get('/sasaran', [ExportController::class, 'exportSasaran'])->name('sasaran');
    Route::get('/pegawai', [ExportController::class, 'exportPegawai'])->name('pegawai');
});

// Notification routes
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
});
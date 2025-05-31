
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KepalaSekolahController;
use App\Http\Controllers\PegawaiController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Pegawai Management
        Route::get('/pegawai', [AdminController::class, 'pegawaiIndex'])->name('pegawai');
        Route::get('/pegawai/create', [AdminController::class, 'pegawaiCreate'])->name('pegawai.create');
        Route::post('/pegawai', [AdminController::class, 'pegawaiStore'])->name('pegawai.store');
        Route::get('/pegawai/{id}/edit', [AdminController::class, 'pegawaiEdit'])->name('pegawai.edit');
        Route::put('/pegawai/{id}', [AdminController::class, 'pegawaiUpdate'])->name('pegawai.update');
        Route::delete('/pegawai/{id}', [AdminController::class, 'pegawaiDestroy'])->name('pegawai.destroy');
        
        // Periode Management
        Route::get('/periode', [AdminController::class, 'periodeIndex'])->name('periode');
        Route::get('/periode/create', [AdminController::class, 'periodeCreate'])->name('periode.create');
        Route::post('/periode', [AdminController::class, 'periodeStore'])->name('periode.store');
        Route::put('/periode/{id}/activate', [AdminController::class, 'periodeActivate'])->name('periode.activate');
        
        // Jabatan Management
        Route::get('/jabatan', [AdminController::class, 'jabatanIndex'])->name('jabatan');
        Route::get('/jabatan/create', [AdminController::class, 'jabatanCreate'])->name('jabatan.create');
        Route::post('/jabatan', [AdminController::class, 'jabatanStore'])->name('jabatan.store');
        
        // Reports
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    });
    
    // Kepala Sekolah Routes
    Route::middleware('role:kepala_sekolah')->prefix('kepala')->name('kepala.')->group(function () {
        Route::get('/dashboard', [KepalaSekolahController::class, 'dashboard'])->name('dashboard');
        
        // Persetujuan SKP
        Route::get('/persetujuan', [KepalaSekolahController::class, 'persetujuan'])->name('persetujuan');
        Route::get('/persetujuan/{id}', [KepalaSekolahController::class, 'persetujuanDetail'])->name('persetujuan.detail');
        Route::post('/persetujuan/{id}/approve', [KepalaSekolahController::class, 'approve'])->name('persetujuan.approve');
        Route::post('/persetujuan/{id}/reject', [KepalaSekolahController::class, 'reject'])->name('persetujuan.reject');
        
        // Monitoring
        Route::get('/monitoring', [KepalaSekolahController::class, 'monitoring'])->name('monitoring');
        
        // Reports
        Route::get('/laporan', [KepalaSekolahController::class, 'laporan'])->name('laporan');
    });
    
    // Pegawai Routes
    Route::middleware('role:guru,staff')->prefix('pegawai')->name('pegawai.')->group(function () {
        Route::get('/dashboard', [PegawaiController::class, 'dashboard'])->name('dashboard');
        
        // Sasaran Kerja
        Route::get('/sasaran', [PegawaiController::class, 'sasaranIndex'])->name('sasaran');
        Route::get('/sasaran/create', [PegawaiController::class, 'sasaranCreate'])->name('sasaran.create');
        Route::post('/sasaran', [PegawaiController::class, 'sasaranStore'])->name('sasaran.store');
        Route::get('/sasaran/{id}/edit', [PegawaiController::class, 'sasaranEdit'])->name('sasaran.edit');
        Route::put('/sasaran/{id}', [PegawaiController::class, 'sasaranUpdate'])->name('sasaran.update');
        
        // Realisasi Kerja
        Route::get('/realisasi', [PegawaiController::class, 'realisasiIndex'])->name('realisasi');
        Route::get('/realisasi/create', [PegawaiController::class, 'realisasiCreate'])->name('realisasi.create');
        Route::post('/realisasi', [PegawaiController::class, 'realisasiStore'])->name('realisasi.store');
        
        // Penilaian
        Route::get('/penilaian', [PegawaiController::class, 'penilaian'])->name('penilaian');
        
        // Rencana Tindak Lanjut
        Route::get('/rencana', [PegawaiController::class, 'rencanaIndex'])->name('rencana');
        Route::post('/rencana', [PegawaiController::class, 'rencanaStore'])->name('rencana.store');
    });
});

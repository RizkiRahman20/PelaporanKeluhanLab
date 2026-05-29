<?php

use App\Http\Controllers\MahasiswaLaporanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIK - MAHASISWA
|--------------------------------------------------------------------------
*/

// HALAMAN HOME
Route::get('/', [MahasiswaLaporanController::class, 'home'])
    ->name('mahasiswa.home');

// HALAMAN FORM LAPORAN
Route::get('/buat-laporan', [MahasiswaLaporanController::class, 'index'])
    ->name('mahasiswa.form');

// Submit form laporan
Route::post('/laporan', [MahasiswaLaporanController::class, 'store'])
    ->name('mahasiswa.store');

// Halaman cek status laporan
Route::get('/status', [MahasiswaLaporanController::class, 'status'])
    ->name('mahasiswa.status');

// Cek status satu laporan spesifik
Route::get('/status/{no_laporan}', [MahasiswaLaporanController::class, 'showStatus'])
    ->name('mahasiswa.status.show')
    ->where('no_laporan', 'LPR-[0-9]{8}-[0-9]{4}');
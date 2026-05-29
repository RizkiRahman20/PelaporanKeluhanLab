<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::middleware('auth')->group(function (){
    Route::get('/pdf/riwayat-perbaikan', [PdfController::class, 'cetakRiwayat'])
    ->name('pdf.riwayat-perbaikan');
});

<?php

use Illuminate\Support\Facades\Route;
// PASTIKAN BARIS INI MENGGUNAKAN \Api\
use App\Http\Controllers\Api\BukuController; 
use App\Http\Controllers\Api\RekapController;

// PASTIKAN MENGGUNAKAN apiResource, BUKAN get
Route::apiResource('buku', BukuController::class);

Route::get('rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
Route::get('rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
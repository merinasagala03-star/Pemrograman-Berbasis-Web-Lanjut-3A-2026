<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;


// 1. Halaman Beranda
Route::view('/', 'home')->name('home');

// 2. Halaman Daftar Buku
Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');

// 3. Halaman Detail Buku (route parameter {id})
Route::get('/buku/{id}', [BukuController::class, 'show'])->name('buku.show');

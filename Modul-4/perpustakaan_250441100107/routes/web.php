<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;

Route::view('/', 'home')->name('home');

// Tambahkan 'web.' pada name() agar tidak bentrok dengan API
Route::get('/buku', [BukuController::class, 'index'])->name('web.buku.index');

Route::get('/buku/{id}', [BukuController::class, 'show'])->name('web.buku.show');
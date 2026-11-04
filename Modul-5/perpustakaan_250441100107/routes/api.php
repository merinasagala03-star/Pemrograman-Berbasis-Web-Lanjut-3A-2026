<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RekapController;
use Illuminate\Support\Facades\Route;

// API dipakai halaman /buku lewat Fetch API (same-origin) sehingga memakai
// session login: grup 'web' (session + CSRF) + 'auth'. Tanpa login -> 401 (JSON).
Route::middleware(['web', 'auth'])->group(function () {

    // Baca data: semua role
    Route::get('buku', [BukuController::class, 'index'])
        ->middleware('permission:lihat-daftar-buku')->name('buku.index');
    Route::get('buku/{buku}', [BukuController::class, 'show'])
        ->middleware('permission:lihat-daftar-buku')->name('buku.show');

    // Tambah / ubah / hapus: admin & petugas
    Route::middleware('permission:kelola-data-buku')->group(function () {
        Route::post('buku', [BukuController::class, 'store'])->name('buku.store');
        Route::match(['put', 'patch'], 'buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    });

    // Rekap/laporan: admin & petugas
    Route::middleware('permission:lihat-laporan-perpustakaan')->group(function () {
        Route::get('rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
        Route::get('rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
    });
});

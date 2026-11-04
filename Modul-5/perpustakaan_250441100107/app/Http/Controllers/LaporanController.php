<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        return view('laporan.index', [
            'totalBuku' => Buku::count(),
            'totalAnggota' => Anggota::count(),
            'totalPeminjaman' => Peminjaman::count(),
            'perStatus' => Peminjaman::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'bukuPerKategori' => Kategori::withCount('bukus')->orderByDesc('bukus_count')->get(),
            'terpopuler' => Buku::withCount('peminjamans')->orderByDesc('peminjamans_count')->limit(5)->get(),
        ]);
    }
}

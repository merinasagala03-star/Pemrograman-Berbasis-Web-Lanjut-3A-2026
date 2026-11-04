<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;

class BukuController extends Controller
{
    // Halaman /buku: data buku diambil browser lewat Fetch API (GET /api/buku).
    // Controller hanya menyediakan daftar kategori untuk <select> pada formulir.
    public function index()
    {
        $kategoris = Kategori::orderBy('nama')->get(['id', 'nama']);
        return view('buku.index', compact('kategoris'));
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->withCount('peminjamans')->findOrFail($id);
        return view('buku.show', compact('buku'));
    }
}

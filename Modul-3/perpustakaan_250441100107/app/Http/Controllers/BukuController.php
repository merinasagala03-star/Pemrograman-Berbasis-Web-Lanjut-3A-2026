<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        $bukus = Buku::with('kategori')->withCount('peminjamans')->paginate(10);
        return view('buku.index', compact('bukus'));
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->withCount('peminjamans')->findOrFail($id);
        return view('buku.show', compact('buku'));
    }
}
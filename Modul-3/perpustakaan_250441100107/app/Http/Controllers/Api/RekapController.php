<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Buku;

class RekapController extends Controller
{
    public function bukuPerKategori()
    {
        $data = Kategori::withCount('bukus')->get();
        return response()->json(['data' => $data], 200);
    }

    public function peminjamanPerBuku()
    {
        $data = Buku::withCount('peminjamans')->get();
        return response()->json(['data' => $data], 200);
    }
}
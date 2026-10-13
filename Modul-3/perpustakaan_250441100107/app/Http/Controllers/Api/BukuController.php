<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;

class BukuController extends Controller
{
    // GET: /api/buku
    public function index(Request $request)
    {
        $query = Buku::with('kategori')->withCount('peminjamans');

        if ($request->has('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->cari . '%')
                  ->orWhere('penulis', 'like', '%' . $request->cari . '%');
            });
        }
        if ($request->has('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        if ($request->has('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        $buku = $query->latest()->paginate(10)->withQueryString();
        return response()->json($buku, 200);
    }

    // POST: /api/buku (Ini yang akan memperbaiki error 405 kamu!)
    public function store(StoreBukuRequest $request)
    {
        $buku = Buku::create($request->validated());
        return response()->json($buku, 201);
    }

    // GET: /api/buku/{id}
    public function show($id)
    {
        $buku = Buku::with('kategori')->withCount('peminjamans')->find($id);
        
        if (!$buku) {
            return response()->json(['message' => 'Data Buku tidak ditemukan'], 404);
        }
        
        return response()->json($buku, 200);
    }

    public function update(UpdateBukuRequest $request, $id)
    {
        $buku = Buku::find($id);
        
        if (!$buku) {
            return response()->json(['message' => 'Data Buku tidak ditemukan'], 404);
        }

        $buku->update($request->validated());
        return response()->json($buku, 200);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);
        
        if (!$buku) {
            return response()->json(['message' => 'Data Buku tidak ditemukan'], 404);
        }

        $buku->delete();
        return response()->json(['message' => 'Buku berhasil dihapus'], 200);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    // GET: /api/buku
    public function index(Request $request)
    {
        $query = Buku::with('kategori')->withCount('peminjamans');

        if ($request->has('cari')) {
            $query->where(function ($q) use ($request) {
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

        $buku = $query->latest('id')->paginate(10)->withQueryString();

        // Bentuk respons diatur oleh BukuResource (data + links + meta paginasi)
        return BukuResource::collection($buku);
    }

    // POST: /api/buku
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        $buku = Buku::create($validated)->load('kategori');

        return (new BukuResource($buku))->response()->setStatusCode(201);
    }

    // GET: /api/buku/{id}
    public function show($id)
    {
        $buku = Buku::with('kategori')->withCount('peminjamans')->find($id);

        if (!$buku) {
            return response()->json(['message' => 'Data Buku tidak ditemukan'], 404);
        }

        return new BukuResource($buku);
    }

    // PUT/PATCH: /api/buku/{id}
    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Data Buku tidak ditemukan'], 404);
        }

        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        $buku->update($validated);

        return new BukuResource($buku->load('kategori'));
    }

    // DELETE: /api/buku/{id}
    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Data Buku tidak ditemukan'], 404);
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus'], 200);
    }

    // ---------- Aturan validasi (dipakai store & update) ----------

    private function rules(): array
    {
        return [
            'kategori_id'  => 'required|exists:kategoris,id',
            'judul'        => 'required|string|min:3|max:150',
            'penulis'      => 'required|string|min:3|max:100',
            'tahun_terbit' => 'required|integer|between:1900,' . date('Y'),
        ];
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'exists'   => ':attribute yang dipilih tidak valid.',
            'string'   => ':attribute harus berupa teks.',
            'min'      => ':attribute minimal :min karakter.',
            'max'      => ':attribute maksimal :max karakter.',
            'between'  => ':attribute harus antara :min sampai :max.',
            'integer'  => ':attribute harus berupa bilangan bulat.',
        ];
    }

    private function attributes(): array
    {
        return [
            'kategori_id'  => 'kategori',
            'judul'        => 'judul buku',
            'penulis'      => 'penulis',
            'tahun_terbit' => 'tahun terbit',
        ];
    }
}

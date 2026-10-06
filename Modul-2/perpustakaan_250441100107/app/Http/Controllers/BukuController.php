<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BukuController extends Controller
{
    protected array $bukus = [
        [
            'id'           => 1,
            'judul'        => 'Laskar Pelangi',
            'penulis'      => 'Andrea Hirata',
            'tahun_terbit' => 2005,
            'kategori'     => 'Fiksi',
        ],
        [
            'id'           => 2,
            'judul'        => 'Bumi Manusia',
            'penulis'      => 'Pramoedya Ananta Toer',
            'tahun_terbit' => 1980,
            'kategori'     => 'Sejarah',
        ],
        [
            'id'           => 3,
            'judul'        => 'Filosofi Teras',
            'penulis'      => 'Henry Manampiring',
            'tahun_terbit' => 2018,
            'kategori'     => 'Pengembangan Diri',
        ],
        [
            'id'           => 4,
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'tahun_terbit' => 2008,
            'kategori'     => 'Teknologi',
        ],
        [
            'id'           => 5,
            'judul'        => 'Sapiens: A Brief History of Humankind',
            'penulis'      => 'Yuval Noah Harari',
            'tahun_terbit' => 2011,
            'kategori'     => 'Sains Populer',
        ],
    ];

    /**
     * Menampilkan halaman Daftar Buku (GET /buku, route: buku.index)
     */
    public function index()
    {
        // Passing data ke view menggunakan compact()
        $bukus = $this->bukus;

        return view('buku.index', compact('bukus'));
    }

    public function show($id)
    {
        // Cari buku berdasarkan id yang dikirim lewat URL
        $buku = collect($this->bukus)->firstWhere('id', (int) $id);

        return view('buku.show', compact('buku'));
    }
}

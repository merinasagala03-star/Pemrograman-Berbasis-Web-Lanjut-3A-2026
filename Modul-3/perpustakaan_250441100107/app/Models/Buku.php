<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    // Hanya kolom ini yang boleh diisi
    protected $fillable = [
        'kategori_id',
        'judul',
        'penulis',
        'tahun_terbit',
    ];

    public function kategori() {
        return $this->belongsTo(Kategori::class);
    }

    public function peminjamans() {
        return $this->hasMany(Peminjaman::class); 
    }
}
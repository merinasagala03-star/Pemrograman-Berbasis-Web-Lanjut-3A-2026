<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    public const DIAJUKAN = 'diajukan';
    public const DIPINJAM = 'dipinjam';
    public const DITOLAK = 'ditolak';
    public const DIKEMBALIKAN = 'dikembalikan';

    protected $table = 'peminjamans';

    protected $fillable = [
        'anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali', 'status', 'petugas_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali' => 'date',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}

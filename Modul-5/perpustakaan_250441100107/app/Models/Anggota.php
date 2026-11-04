<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggotas';

    protected $fillable = ['nama', 'email', 'no_telepon'];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'anggota_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'anggota_id');
    }
}

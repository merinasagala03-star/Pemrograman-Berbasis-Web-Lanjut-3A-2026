<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// 'role' sengaja TIDAK ada di $fillable: role hanya boleh ditetapkan lewat
// assignRole()/syncRoles() oleh seeder atau admin, bukan lewat mass-assignment.
#[Fillable(['name', 'email', 'password', 'anggota_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Query peminjaman milik user ini (berdasarkan anggota yang terhubung).
     * Jika belum punya anggota, hasilnya selalu kosong.
     */
    public function peminjamanSaya(): Builder
    {
        return Peminjaman::query()
            ->when(
                $this->anggota_id,
                fn (Builder $q) => $q->where('anggota_id', $this->anggota_id),
                fn (Builder $q) => $q->whereRaw('1 = 0'),
            );
    }

    /** Pastikan user punya data anggota (dibutuhkan untuk meminjam buku). */
    public function pastikanAnggota(): Anggota
    {
        if ($this->anggota) {
            return $this->anggota;
        }

        $anggota = Anggota::firstOrCreate(
            ['email' => $this->email],
            ['nama' => $this->name, 'no_telepon' => '-'],
        );
        $this->forceFill(['anggota_id' => $anggota->id])->save();

        return $anggota;
    }

    /** Nama role utama untuk ditampilkan di UI. */
    public function namaRole(): string
    {
        return $this->getRoleNames()->first() ?? '-';
    }
}

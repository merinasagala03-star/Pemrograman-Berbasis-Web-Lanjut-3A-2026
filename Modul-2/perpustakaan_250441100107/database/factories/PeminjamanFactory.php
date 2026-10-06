<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    public function definition(): array
    {
        return [
            'anggota_id'      => Anggota::inRandomOrder()->first()->id,
            'buku_id'         => Buku::inRandomOrder()->first()->id,
            'tanggal_pinjam'  => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            // boleh kosong (belum dikembalikan)
            'tanggal_kembali' => fake()->optional(0.5)->dateTimeBetween('now', '+14 days'),
        ];
    }
}

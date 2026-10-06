<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    protected $model = Buku::class;

    public function definition(): array
    {
        return [
            // ambil id dari data kategori yang sudah ada
            'kategori_id'  => Kategori::inRandomOrder()->first()->id,
            'judul'        => fake()->sentence(3),
            'penulis'      => fake()->name(),
            'tahun_terbit' => fake()->numberBetween(1990, 2025),
        ];
    }
}

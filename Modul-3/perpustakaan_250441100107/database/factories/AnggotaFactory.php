<?php

namespace Database\Factories;

use App\Models\Anggota;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anggota>
 */
class AnggotaFactory extends Factory
{
    protected $model = Anggota::class;

    public function definition(): array
    {
        return [
            'nama'       => fake()->name(),
            'email'      => fake()->unique()->safeEmail(),
            'no_telepon' => fake()->numerify('08##########'),
        ];
    }
}

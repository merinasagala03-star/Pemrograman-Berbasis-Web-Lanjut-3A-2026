<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarKategori = [
            'Fiksi',
            'Sejarah',
            'Pengembangan Diri',
            'Sains',
            'Teknologi',
            'Biografi',
            'Agama',
        ];

        foreach ($daftarKategori as $nama) {
            // firstOrCreate: aman dijalankan berulang, tidak membuat kategori ganda
            Kategori::firstOrCreate(['nama' => $nama]);
        }
    }
}
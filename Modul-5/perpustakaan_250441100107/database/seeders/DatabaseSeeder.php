<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting karena ada foreign key:
        // tabel induk dulu, baru tabel yang bergantung padanya.
        $this->call([
            RolePermissionSeeder::class, // role & permission (Spatie)
            KategoriSeeder::class,
            AnggotaSeeder::class,
            BukuSeeder::class,
            PeminjamanSeeder::class,
            UserSeeder::class,           // akun admin, petugas, siswa
        ]);
    }
}

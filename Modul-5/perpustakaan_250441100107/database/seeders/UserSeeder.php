<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Akun demo. Role ditetapkan di sini (jalur berwenang), bukan dari formulir registrasi.
     * Semua password demo: "password" — GANTI sebelum dipakai di luar latihan.
     */
    public function run(): void
    {
        $akun = [
            ['Admin Perpustakaan',  'admin@perpustakaan.test',   'admin',   '081200000001'],
            ['Petugas Perpustakaan', 'petugas@perpustakaan.test', 'petugas', '081200000002'],
            ['Siswa Satu',          'siswa1@perpustakaan.test',  'siswa',   '081200000003'],
            ['Siswa Dua',           'siswa2@perpustakaan.test',  'siswa',   '081200000004'],
        ];

        foreach ($akun as [$nama, $email, $role, $telepon]) {
            $anggota = Anggota::firstOrCreate(
                ['email' => $email],
                ['nama' => $nama, 'no_telepon' => $telepon],
            );

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'anggota_id' => $anggota->id,
                ],
            );

            $user->syncRoles([$role]);
        }

        // Contoh riwayat peminjaman agar halaman "riwayat" tidak kosong saat demo.
        $buku = Buku::orderBy('id')->take(2)->get();
        foreach (['siswa1@perpustakaan.test' => 0, 'siswa2@perpustakaan.test' => 1] as $email => $i) {
            $user = User::where('email', $email)->first();
            if ($user && isset($buku[$i]) && ! $user->peminjamanSaya()->exists()) {
                Peminjaman::create([
                    'anggota_id' => $user->anggota_id,
                    'buku_id' => $buku[$i]->id,
                    'tanggal_pinjam' => now()->subDays(10)->toDateString(),
                    'tanggal_kembali' => now()->subDays(3)->toDateString(),
                    'status' => Peminjaman::DIKEMBALIKAN,
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Matriks hak akses sesuai tabel pada soal nomor 4.
     * Kunci = nama permission, nilai = role yang memilikinya.
     */
    public const MATRIKS = [
        'akses-dashboard-admin'              => ['admin'],
        'akses-dashboard-petugas'            => ['petugas'],
        'lihat-daftar-buku'                  => ['admin', 'petugas', 'siswa'],
        'kelola-data-buku'                   => ['admin', 'petugas'],
        'kelola-data-pengguna'               => ['admin'],
        'kelola-role-pengguna'               => ['admin'],
        'kelola-peminjaman'                  => ['admin', 'petugas'],
        'kelola-pengembalian'                => ['admin', 'petugas'],
        'mengajukan-peminjaman-buku'         => ['admin', 'petugas', 'siswa'],
        'memproses-persetujuan-peminjaman'   => ['admin', 'petugas'],
        'mencatat-pengembalian-buku'         => ['admin', 'petugas'],
        'melihat-status-peminjaman-sendiri'  => ['admin', 'petugas', 'siswa'],
        'melihat-riwayat-peminjaman-sendiri' => ['admin', 'petugas', 'siswa'],
        'lihat-laporan-perpustakaan'         => ['admin', 'petugas'],
        'lihat-profil-sendiri'               => ['admin', 'petugas', 'siswa'],
        'ubah-profil-sendiri'                => ['admin', 'petugas', 'siswa'],
    ];

    public function run(): void
    {
        // Kosongkan cache permission agar perubahan seeder langsung terbaca.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [];
        foreach (['admin', 'petugas', 'siswa'] as $nama) {
            $roles[$nama] = Role::findOrCreate($nama, 'web');
        }

        foreach (self::MATRIKS as $permission => $daftarRole) {
            Permission::findOrCreate($permission, 'web');
        }

        // syncPermissions membuat seeder aman dijalankan berulang kali (idempotent).
        foreach ($roles as $nama => $role) {
            $role->syncPermissions(
                array_keys(array_filter(self::MATRIKS, fn ($r) => in_array($nama, $r, true)))
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

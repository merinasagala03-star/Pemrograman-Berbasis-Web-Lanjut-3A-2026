<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Riwayat peminjaman milik $target boleh dilihat oleh:
     *  - pemiliknya sendiri (pemeriksaan IDENTITAS, bukan hanya permission), atau
     *  - staf yang berhak mengelola peminjaman (admin/petugas).
     * Siswa yang mencoba membuka riwayat siswa lain -> ditolak (403).
     */
    public function viewRiwayat(User $auth, User $target): bool
    {
        if ($auth->is($target)) {
            return $auth->can('melihat-riwayat-peminjaman-sendiri');
        }

        return $auth->can('kelola-peminjaman');
    }
}

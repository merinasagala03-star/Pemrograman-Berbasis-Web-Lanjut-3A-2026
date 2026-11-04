# Laporan Pengujian RBAC — Sistem Perpustakaan Digital (Modul V)

Pengujian dijalankan otomatis (`php artisan test`, `tests/Feature/RbacTest.php` & `AuthTest.php`)
dan dilengkapi uji manual lewat HTTP (login → cookie session → CSRF → logout).

**Hasil run otomatis (`php artisan test`): 54 test, 282 assertion, semua lulus.**
Lingkungan uji: PHP 8.3, Laravel 13.35, Spatie Laravel Permission (branch `main`), SQLite in-memory.
Jalankan ulang di komputer Anda (MySQL) dan lampirkan tangkapan layar untuk bukti dikumpulkan.

| No | Role | Skenario Pengujian | Hasil yang Diharapkan | Hasil Aktual | Test |
|----|------|--------------------|-----------------------|--------------|------|
| 1 | Belum login | Membuka dashboard admin | Dialihkan ke halaman login | Sesuai: redirect 302 ke `/login` | `test_s1_...` |
| 2 | Admin | Membuka dashboard admin | Dashboard admin dapat diakses | Sesuai: HTTP 200 | `test_s2_...` |
| 3 | Admin | Menambah, mengubah, menghapus buku | Operasi berhasil | Sesuai: POST 201, PUT 200, DELETE 200 | `test_s3_...` |
| 4 | Admin | Mengelola akun dan role pengguna | Operasi berhasil | Sesuai: tambah/ubah role/hapus berhasil | `test_s4_...` |
| 5 | Admin | Mengakses peminjaman, pengembalian, laporan | Data dapat diakses | Sesuai: HTTP 200 semua halaman | `test_s5_...` |
| 6 | Petugas | Membuka dashboard petugas | Dashboard petugas dapat diakses | Sesuai: HTTP 200 (dashboard admin → 403) | `test_s6_...` |
| 7 | Petugas | Mengelola data buku | Operasi berhasil | Sesuai: POST/PUT/DELETE berhasil | `test_s7_...` |
| 8 | Petugas | Memproses peminjaman dan pengembalian | Operasi berhasil | Sesuai: diajukan → dipinjam → dikembalikan | `test_s8_...` |
| 9 | Petugas | Membuka pengelolaan akun/role | Akses ditolak | Sesuai: HTTP 403 (index, create, edit, update, delete) | `test_s9_...` |
| 10 | Siswa | Melihat daftar & detail buku | Data buku dapat dilihat | Sesuai: HTTP 200 | `test_s10_...` |
| 11 | Siswa | Melihat & mengubah profil sendiri | Profil sendiri dapat diakses & diperbarui | Sesuai: data user & anggota terbarui | `test_s11_...` |
| 12 | Siswa | Melihat riwayat peminjaman sendiri | Riwayat milik sendiri dapat dilihat | Sesuai: HTTP 200, hanya data sendiri | `test_s12_...` |
| 13 | Siswa | Membuka dashboard admin / kelola buku | Akses ditolak | Sesuai: HTTP 403 (halaman & API langsung) | `test_s13_...` |
| 14 | Siswa | Melihat riwayat siswa lain | Akses ditolak | Sesuai: HTTP 403 (`/riwayat-peminjaman/{id lain}`) | `test_s14_...` |
| 15 | Semua | Logout lalu buka halaman terlindungi | Harus login kembali | Sesuai: redirect ke `/login` | `test_s15_...` |

## Pengujian tambahan

- Registrasi dengan `role=admin` pada request tetap menghasilkan role `siswa`.
- Password tersimpan sebagai hash (bcrypt), bukan teks asli.
- Kredensial salah → tidak login; 5× gagal → throttle.
- `/login` & `/register` saat sudah login → dialihkan ke `/dashboard` (middleware `guest`).
- `/api/buku` tanpa login → 401; tanpa token CSRF → 419; siswa → 403.
- Pengajuan peminjaman tidak bisa atas nama orang lain (`anggota_id` diambil dari akun login).
- Profil selalu milik akun login; `id`/`role`/`anggota_id` yang dikirim di request diabaikan.
- Admin tidak bisa menurunkan role atau menghapus akunnya sendiri.
- Menu & tombol disembunyikan dengan `@can` (siswa tidak melihat form tambah/ubah/hapus buku).

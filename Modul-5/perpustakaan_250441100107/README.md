<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


---

# Modul V — Autentikasi & RBAC

Melanjutkan proyek Modul sebelumnya (fitur buku/API/rekap tetap dipertahankan) dengan
**Laravel Breeze (stack Blade)** untuk autentikasi dan **Spatie Laravel Permission** untuk RBAC.

## Instalasi (urutan)

```bash
composer require spatie/laravel-permission
composer require laravel/breeze --dev      # opsional: scaffolding Breeze sudah ada di proyek ini
composer install
npm install && npm run build               # WAJIB agar CSS baru ikut ter-build
php artisan config:clear
php artisan migrate:fresh --seed           # buat tabel + role/permission + akun demo
php artisan serve
```

> **Jangan** menjalankan `php artisan breeze:install`: perintah itu menimpa `routes/web.php`,
> `layouts/app.blade.php`, dan `resources/css/app.css` milik proyek ini. File-file Breeze
> (controller Auth, `LoginRequest`, `routes/auth.php`, view login/registrasi/profil) sudah
> disalin dan **disesuaikan** dengan layout & struktur tabel proyek.
>
> **Jangan** menjalankan `vendor:publish` untuk *migration* Spatie — migration tabel
> permission sudah ada di `database/migrations/2026_10_30_000001_create_permission_tables.php`.
> (Config boleh: `php artisan vendor:publish --tag=permission-config`.)

## Akun demo (password: `password`)

| Role    | Email                          |
|---------|--------------------------------|
| Admin   | admin@perpustakaan.test        |
| Petugas | petugas@perpustakaan.test      |
| Siswa   | siswa1@perpustakaan.test       |
| Siswa   | siswa2@perpustakaan.test       |

Registrasi mandiri (`/register`) **selalu** menghasilkan role `siswa`. Field `role` dari request diabaikan.
Role hanya bisa ditetapkan lewat seeder atau halaman admin `/admin/pengguna`.

## Perubahan pada struktur proyek

| Berkas | Keterangan |
|---|---|
| `2026_10_30_000001_create_permission_tables` | Tabel Spatie (roles, permissions, pivot) |
| `2026_10_30_000002_add_anggota_id_to_users_table` | Menghubungkan akun login (`users`) dengan `anggotas` |
| `2026_10_30_000003_add_status_to_peminjamans_table` | Kolom `status` (diajukan/dipinjam/ditolak/dikembalikan) & `petugas_id` untuk alur persetujuan |
| `RolePermissionSeeder`, `UserSeeder` | 3 role, 16 permission, akun demo |
| `routes/auth.php`, `app/Http/Controllers/Auth/*` | Registrasi, login, logout (Breeze) |
| `app/Policies/UserPolicy.php` | Cek **identitas** untuk riwayat peminjaman |
| `bootstrap/app.php` | Alias middleware `role`, `permission`, `role_or_permission`; redirect `guest` ke `/dashboard` |

## Penerapan tiap soal

1. **Breeze** — registrasi/login/logout; password disimpan dengan `Hash::make` (bcrypt) + cast `hashed`; registrasi membuat baris `anggotas` + `users` yang saling terhubung.
2. **Session & middleware** — `auth` melindungi semua halaman internal, `guest` pada `/login` & `/register`; logout memanggil `session()->invalidate()` + `regenerateToken()`.
3. **RBAC 3 role** — admin, petugas, siswa (lihat tabel di soal).
4. **Spatie** — 16 permission pada `RolePermissionSeeder::MATRIKS`; profil & riwayat dicek juga berdasarkan identitas (`$request->user()` / `UserPolicy`).
5. **Proteksi route & Blade** — `->middleware('permission:...')` pada route, `@can(...)` pada menu/tombol. `/api/buku` kini memerlukan login (+ permission) karena memakai session (grup `web`) — halaman `/buku` mengirim header `X-CSRF-TOKEN` pada Fetch API.

## Pengujian

```bash
php artisan test
```
`tests/Feature/AuthTest.php` & `tests/Feature/RbacTest.php` mencakup ke-15 skenario soal nomor 5. Lihat `LAPORAN_PENGUJIAN.md`.

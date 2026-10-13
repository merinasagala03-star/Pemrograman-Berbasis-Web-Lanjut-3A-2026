<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan — @yield('title', 'Beranda')</title>

    @vite('resources/css/app.css')
</head>
<body>

    <header class="site-header">
        <span class="site-title__mark">Katalog</span>
        <h1 class="site-title">Perpustakaan</h1>
    </header>

    <nav class="site-navbar">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('buku.index') }}" class="{{ request()->routeIs('buku.*') ? 'active' : '' }}">Daftar Buku</a>
    </nav>

    <main class="site-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        Perpustakaan Digital — Tugas Praktikum PBWL Modul I
    </footer>

</body>
</html>

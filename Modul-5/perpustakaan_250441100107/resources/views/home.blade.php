@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="hero">
        <p class="hero__kicker">Ruang Baca Digital</p>
        <h2>Cari buku, baca ceritanya, pulangkan tepat waktu.</h2>
        <p>
            Katalog ini menyimpan koleksi buku perpustakaan kami — dari fiksi
            klasik sampai referensi teknis. Masuk untuk menelusuri judul,
            penulis, dan tahun terbitnya, serta mengajukan peminjaman.
        </p>

        @auth
            <a href="{{ route('dashboard') }}" class="btn-primary">Buka Halaman Saya</a>
            @can('lihat-daftar-buku')
                <a href="{{ route('web.buku.index') }}" class="btn-sekunder btn-link">Daftar Buku</a>
            @endcan
        @else
            <a href="{{ route('login') }}" class="btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn-sekunder btn-link">Registrasi</a>
        @endauth
    </section>
@endsection

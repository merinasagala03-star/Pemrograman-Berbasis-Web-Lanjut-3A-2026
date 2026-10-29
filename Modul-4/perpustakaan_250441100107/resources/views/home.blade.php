@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="hero">
        <p class="hero__kicker">Ruang Baca Digital</p>
        <h2>Cari buku, baca ceritanya, pulangkan tepat waktu.</h2>
        <p>
            Katalog ini menyimpan koleksi buku perpustakaan kami — dari fiksi
            klasik sampai referensi teknis. Buka daftar buku untuk menelusuri
            judul, penulis, dan tahun terbitnya.
        </p>
        <!-- INI BAGIAN YANG DIPERBAIKI: Link diubah menjadi /buku -->
        <a href="/buku" class="btn-primary">Buka Daftar Buku</a>
    </section>
@endsection
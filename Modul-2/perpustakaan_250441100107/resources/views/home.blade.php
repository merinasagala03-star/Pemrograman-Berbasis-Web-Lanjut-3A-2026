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
        <a href="{{ route('buku.index') }}" class="btn-primary">Buka Daftar Buku</a>
    </section>
@endsection
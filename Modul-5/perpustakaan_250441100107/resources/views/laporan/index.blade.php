@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')

@section('content')
    <h2>Laporan Perpustakaan</h2>

    <div class="stat-grid">
        <div class="stat"><span class="stat__angka">{{ $totalBuku }}</span><span class="stat__label">Judul buku</span></div>
        <div class="stat"><span class="stat__angka">{{ $totalAnggota }}</span><span class="stat__label">Anggota</span></div>
        <div class="stat"><span class="stat__angka">{{ $totalPeminjaman }}</span><span class="stat__label">Total peminjaman</span></div>
    </div>

    <h3>Peminjaman per status</h3>
    <ul class="daftar">
        @foreach (['diajukan', 'dipinjam', 'ditolak', 'dikembalikan'] as $s)
            <li><span class="badge badge--{{ $s }}">{{ $s }}</span> {{ $perStatus[$s] ?? 0 }}</li>
        @endforeach
    </ul>

    <h3>Buku per kategori</h3>
    <ul class="daftar">
        @foreach ($bukuPerKategori as $k)
            <li>{{ $k->nama }} — {{ $k->bukus_count }} buku</li>
        @endforeach
    </ul>

    <h3>Buku paling sering dipinjam</h3>
    <ul class="daftar">
        @forelse ($terpopuler as $b)
            <li>{{ $b->judul }} — {{ $b->peminjamans_count }}×</li>
        @empty
            <li>Belum ada data.</li>
        @endforelse
    </ul>
@endsection

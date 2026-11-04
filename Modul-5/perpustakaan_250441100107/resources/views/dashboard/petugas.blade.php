@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
    <h2>Dashboard Petugas</h2>
    <p class="section-note">Operasional harian perpustakaan.</p>

    <div class="stat-grid">
        <div class="stat"><span class="stat__angka">{{ $totalBuku }}</span><span class="stat__label">Judul buku</span></div>
        <div class="stat"><span class="stat__angka">{{ $diajukan }}</span><span class="stat__label">Menunggu persetujuan</span></div>
        <div class="stat"><span class="stat__angka">{{ $dipinjam }}</span><span class="stat__label">Sedang dipinjam</span></div>
    </div>

    <p class="menu-cepat">
        <a class="btn-primary" href="{{ route('web.buku.index') }}">Kelola Buku</a>
        <a class="btn-primary" href="{{ route('peminjaman.index', ['status' => 'diajukan']) }}">Proses Peminjaman</a>
        <a class="btn-primary" href="{{ route('pengembalian.index') }}">Catat Pengembalian</a>
        <a class="btn-primary" href="{{ route('laporan.index') }}">Laporan</a>
    </p>
@endsection

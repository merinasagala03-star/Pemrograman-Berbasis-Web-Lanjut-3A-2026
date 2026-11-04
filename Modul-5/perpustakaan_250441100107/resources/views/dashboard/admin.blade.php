@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <h2>Dashboard Admin</h2>
    <p class="section-note">Ringkasan seluruh kebutuhan administrasi perpustakaan.</p>

    <div class="stat-grid">
        <div class="stat"><span class="stat__angka">{{ $totalBuku }}</span><span class="stat__label">Judul buku</span></div>
        <div class="stat"><span class="stat__angka">{{ $totalPengguna }}</span><span class="stat__label">Pengguna</span></div>
        <div class="stat"><span class="stat__angka">{{ $diajukan }}</span><span class="stat__label">Menunggu persetujuan</span></div>
        <div class="stat"><span class="stat__angka">{{ $dipinjam }}</span><span class="stat__label">Sedang dipinjam</span></div>
        <div class="stat"><span class="stat__angka">{{ $dikembalikan }}</span><span class="stat__label">Sudah dikembalikan</span></div>
    </div>

    <h3>Pengguna per role</h3>
    <ul class="daftar">
        @foreach ($penggunaPerRole as $role => $jumlah)
            <li><span class="badge badge--role">{{ $role }}</span> {{ $jumlah }} pengguna</li>
        @endforeach
    </ul>

    <p class="menu-cepat">
        <a class="btn-primary" href="{{ route('web.buku.index') }}">Kelola Buku</a>
        <a class="btn-primary" href="{{ route('admin.users.index') }}">Kelola Pengguna &amp; Role</a>
        <a class="btn-primary" href="{{ route('peminjaman.index') }}">Peminjaman</a>
        <a class="btn-primary" href="{{ route('pengembalian.index') }}">Pengembalian</a>
        <a class="btn-primary" href="{{ route('laporan.index') }}">Laporan</a>
    </p>
@endsection

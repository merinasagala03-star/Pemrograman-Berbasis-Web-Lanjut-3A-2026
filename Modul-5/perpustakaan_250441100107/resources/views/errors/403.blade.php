@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="not-found">
        <h2>403 — Akses Ditolak</h2>
        <p>Anda tidak memiliki hak akses untuk membuka halaman atau menjalankan operasi ini.</p>
        <p><a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn-sekunder btn-link">Kembali</a></p>
    </div>
@endsection

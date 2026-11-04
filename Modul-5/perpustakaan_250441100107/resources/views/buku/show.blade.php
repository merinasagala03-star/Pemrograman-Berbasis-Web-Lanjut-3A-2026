@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
    <div class="detail-buku">
        <div class="detail-buku__row">
            <span class="detail-buku__label">Judul</span>
            <span class="detail-buku__value detail-buku__value--title">{{ $buku->judul }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Penulis</span>
            <span class="detail-buku__value">{{ $buku->penulis }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Tahun terbit</span>
            <span class="detail-buku__value">{{ $buku->tahun_terbit }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Kategori</span>
            <span class="detail-buku__value">{{ $buku->kategori->nama ?? '-' }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Dipinjam</span>
            <span class="detail-buku__value">{{ $buku->peminjamans_count }}×</span>
        </div>
    </div>

    <p class="menu-cepat">
        @can('mengajukan-peminjaman-buku')
            <form method="POST" action="{{ route('peminjaman.ajukan', $buku->id) }}" class="inline-form">
                @csrf
                <button type="submit" class="btn-primary">Ajukan Peminjaman</button>
            </form>
        @endcan
        <a href="{{ route('web.buku.index') }}" class="btn-sekunder btn-link">« Kembali ke daftar</a>
    </p>
@endsection

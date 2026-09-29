@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
    @if ($buku)
        <h2>Detail Buku</h2>
        <p class="section-note">Kartu katalog nomor {{ $buku['id'] }}.</p>

        <div class="detail-buku">
            <div class="detail-buku__row">
                <span class="detail-buku__label">Judul</span>
                <span class="detail-buku__value detail-buku__value--title">{{ $buku['judul'] }}</span>
            </div>
            <div class="detail-buku__row">
                <span class="detail-buku__label">Penulis</span>
                <span class="detail-buku__value">{{ $buku['penulis'] }}</span>
            </div>
            <div class="detail-buku__row">
                <span class="detail-buku__label">Tahun Terbit</span>
                <span class="detail-buku__value">{{ $buku['tahun_terbit'] }}</span>
            </div>
            <div class="detail-buku__row">
                <span class="detail-buku__label">Kategori</span>
                <span class="detail-buku__value">{{ $buku['kategori'] }}</span>
            </div>
        </div>

        <a href="{{ route('buku.index') }}" class="btn-detail">&larr; Kembali ke Daftar Buku</a>
    @else
        <div class="not-found">
            <h2>Kartu Tidak Ditemukan</h2>
            <p>Buku dengan ID tersebut tidak ada di katalog kami.</p>
            <a href="{{ route('buku.index') }}" class="btn-detail">&larr; Kembali ke Daftar Buku</a>
        </div>
    @endif
@endsection

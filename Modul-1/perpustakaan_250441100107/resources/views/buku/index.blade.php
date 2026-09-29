@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <h2>Daftar Buku</h2>
    <p class="section-note">{{ count($bukus) }} judul tersedia di katalog.</p>

    <div class="grid-buku">
        @foreach ($bukus as $buku)
            <x-kartu-buku
                :judul="$buku['judul']"
                :penulis="$buku['penulis']"
                :tahun="$buku['tahun_terbit']"
            >
                <x-slot:kategori>
                    {{ $buku['kategori'] }}
                </x-slot:kategori>

                <a href="{{ route('buku.show', $buku['id']) }}" class="btn-detail">
                    Lihat detail
                </a>
            </x-kartu-buku>
        @endforeach
    </div>
@endsection

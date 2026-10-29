@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <h2>Daftar Buku</h2>
    
    <!-- PERBAIKAN 1: Gunakan total() karena datanya di-paginate dari controller -->
    <p class="section-note">{{ $bukus->total() }} judul tersedia di katalog.</p>

    <div class="grid-buku">
        @foreach ($bukus as $buku)
            <x-kartu-buku
                :judul="$buku->judul"
                :penulis="$buku->penulis"
                :tahun="$buku->tahun_terbit"
            >
                <x-slot:kategori>
                    {{ $buku->kategori->nama ?? 'Tidak ada kategori' }}
                </x-slot:kategori>

                <!-- PERBAIKAN 2: Gunakan URL manual yang dijamin 100% bebas bentrok dengan API -->
                <a href="/buku/{{ $buku->id }}" class="btn-detail">
                    Lihat detail
                </a>
            </x-kartu-buku>
        @endforeach
    </div>

    <!-- PERBAIKAN 3: Menambahkan link navigasi halaman (Pagination) -->
    <div style="margin-top: 20px;">
        {{ $bukus->links() }}
    </div>
@endsection
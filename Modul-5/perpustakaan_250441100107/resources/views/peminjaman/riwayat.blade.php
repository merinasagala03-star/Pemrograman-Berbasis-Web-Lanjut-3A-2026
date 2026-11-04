@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
    <h2>{{ $milikSendiri ? 'Riwayat Peminjaman Saya' : 'Riwayat Peminjaman: '.$pemilik->name }}</h2>

    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead><tr><th>No</th><th>Buku</th><th>Tgl pinjam</th><th>Tgl kembali</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($peminjamans as $p)
                    <tr>
                        <td>{{ $peminjamans->firstItem() + $loop->index }}</td>
                        <td>{{ $p->buku->judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d-m-Y') }}</td>
                        <td>{{ $p->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>
                        <td><span class="badge badge--{{ $p->status }}">{{ $p->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="tabel-buku__info">Belum ada riwayat peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="paginasi-laravel">{{ $peminjamans->links() }}</div>
@endsection

@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
    <h2>Status Peminjaman Saya</h2>
    <p class="section-note">Pengajuan dan peminjaman yang sedang berjalan. Lihat <a href="{{ route('riwayat.saya') }}">riwayat lengkap</a>.</p>

    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead><tr><th>No</th><th>Buku</th><th>Tgl pinjam</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($peminjamans as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->buku->judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d-m-Y') }}</td>
                        <td><span class="badge badge--{{ $p->status }}">{{ $p->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="tabel-buku__info">Tidak ada peminjaman yang sedang berjalan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Pengembalian Buku')

@section('content')
    <h2>Pengembalian Buku</h2>

    <h3>Sedang dipinjam</h3>
    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead><tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl pinjam</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($dipinjam as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->anggota->nama ?? '-' }}</td>
                        <td>{{ $p->buku->judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d-m-Y') }}</td>
                        <td class="tabel-buku__aksi">
                            @can('mencatat-pengembalian-buku')
                                <form method="POST" action="{{ route('pengembalian.kembalikan', $p) }}" class="inline-form">
                                    @csrf <button type="submit" class="btn-ubah">Catat kembali</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="tabel-buku__info">Tidak ada buku yang sedang dipinjam.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3>Pengembalian terakhir</h3>
    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead><tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl kembali</th></tr></thead>
            <tbody>
                @forelse ($selesai as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->anggota->nama ?? '-' }}</td>
                        <td>{{ $p->buku->judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_kembali?->format('d-m-Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="tabel-buku__info">Belum ada pengembalian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

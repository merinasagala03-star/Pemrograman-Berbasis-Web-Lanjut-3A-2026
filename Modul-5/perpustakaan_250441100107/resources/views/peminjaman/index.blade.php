@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content')
    <h2>Data Peminjaman</h2>

    <p class="filter">
        Filter:
        <a href="{{ route('peminjaman.index') }}" class="{{ ! $status ? 'active' : '' }}">Semua</a>
        @foreach (['diajukan', 'dipinjam', 'ditolak', 'dikembalikan'] as $s)
            <a href="{{ route('peminjaman.index', ['status' => $s]) }}" class="{{ $status === $s ? 'active' : '' }}">{{ ucfirst($s) }}</a>
        @endforeach
    </p>

    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead>
                <tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl pinjam</th><th>Tgl kembali</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($peminjamans as $p)
                    <tr>
                        <td>{{ $peminjamans->firstItem() + $loop->index }}</td>
                        <td>
                            {{ $p->anggota->nama ?? '-' }}
                            @if ($p->anggota?->user)
                                <a href="{{ route('riwayat.user', $p->anggota->user->id) }}">(riwayat)</a>
                            @endif
                        </td>
                        <td>{{ $p->buku->judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d-m-Y') }}</td>
                        <td>{{ $p->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>
                        <td><span class="badge badge--{{ $p->status }}">{{ $p->status }}</span></td>
                        <td class="tabel-buku__aksi">
                            @can('memproses-persetujuan-peminjaman')
                                @if ($p->status === 'diajukan')
                                    <form method="POST" action="{{ route('peminjaman.setujui', $p) }}" class="inline-form">
                                        @csrf <button type="submit" class="btn-ubah">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('peminjaman.tolak', $p) }}" class="inline-form">
                                        @csrf <button type="submit" class="btn-hapus">Tolak</button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="tabel-buku__info">Belum ada data peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="paginasi-laravel">{{ $peminjamans->links() }}</div>
@endsection

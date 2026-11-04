@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
    <h2>Kelola Pengguna &amp; Role</h2>
    <p><a href="{{ route('admin.users.create') }}" class="btn-primary">+ Tambah Pengguna</a></p>

    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead><tr><th>No</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge badge--role">{{ $u->namaRole() }}</span></td>
                        <td class="tabel-buku__aksi">
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn-ubah">Ubah</a>
                            <a href="{{ route('riwayat.user', $u) }}" class="btn-ubah">Riwayat</a>
                            @if (! $u->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-form"
                                      onsubmit="return confirm('Hapus pengguna {{ e($u->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-hapus">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="paginasi-laravel">{{ $users->links() }}</div>
@endsection

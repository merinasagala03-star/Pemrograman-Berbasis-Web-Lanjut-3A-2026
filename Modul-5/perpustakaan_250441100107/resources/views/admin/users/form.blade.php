@extends('layouts.app')

@php($edit = $user->exists)

@section('title', $edit ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('content')
    <h2>{{ $edit ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h2>

    <form method="POST" action="{{ $edit ? route('admin.users.update', $user) : route('admin.users.store') }}" class="form-buku">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="form-buku__grid">
            <div class="form-buku__field">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <small class="form-buku__error">{{ $message }}</small> @enderror
            </div>
            <div class="form-buku__field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email') <small class="form-buku__error">{{ $message }}</small> @enderror
            </div>
            <div class="form-buku__field">
                <label for="no_telepon">No. telepon</label>
                <input type="text" id="no_telepon" name="no_telepon" maxlength="20"
                       value="{{ old('no_telepon', $user->anggota->no_telepon ?? '') }}" required>
                @error('no_telepon') <small class="form-buku__error">{{ $message }}</small> @enderror
            </div>
            <div class="form-buku__field">
                <label for="password">Kata sandi {{ $edit ? '(kosongkan jika tidak diganti)' : '' }}</label>
                <input type="password" id="password" name="password" autocomplete="new-password" {{ $edit ? '' : 'required' }}>
                @error('password') <small class="form-buku__error">{{ $message }}</small> @enderror
            </div>

            {{-- Pemilihan role hanya muncul bagi yang punya permission kelola-role-pengguna --}}
            @can('kelola-role-pengguna')
                <div class="form-buku__field">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        @foreach ($roles as $r)
                            <option value="{{ $r->name }}" @selected(old('role', $user->getRoleNames()->first() ?? 'siswa') === $r->name)>{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                    @error('role') <small class="form-buku__error">{{ $message }}</small> @enderror
                </div>
            @endcan
        </div>

        <div class="form-buku__aksi">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="btn-sekunder btn-link">Batal</a>
        </div>
    </form>
@endsection

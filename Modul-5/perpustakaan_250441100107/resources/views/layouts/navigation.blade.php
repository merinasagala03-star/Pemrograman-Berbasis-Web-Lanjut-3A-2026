{{--
    Navigasi utama. Menu ditampilkan berdasarkan permission memakai @can (Soal 5):
    admin, petugas, dan siswa masing-masing hanya melihat menu yang boleh mereka akses.
    Catatan: ini hanya tampilan; penolakan sebenarnya dilakukan middleware pada route.
--}}
@php
    $link = fn (bool $aktif) => $aktif
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium text-gray-900'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
    $linkM = fn (bool $aktif) => $aktif
        ? 'block ps-3 pe-4 py-2 border-s-4 border-indigo-400 text-base font-medium text-indigo-700 bg-indigo-50'
        : 'block ps-3 pe-4 py-2 border-s-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300';
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="font-bold text-gray-800">
                        📚 Perpustakaan Digital
                    </a>
                </div>

                {{-- Menu desktop --}}
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex">
                    @auth
                        @can('akses-dashboard-admin')<a href="{{ route('admin.dashboard') }}" class="{{ $link(request()->routeIs('admin.dashboard')) }}">Dashboard Admin</a>@endcan
                        @can('akses-dashboard-petugas')<a href="{{ route('petugas.dashboard') }}" class="{{ $link(request()->routeIs('petugas.dashboard')) }}">Dashboard Petugas</a>@endcan
                        @can('lihat-daftar-buku')<a href="{{ route('web.buku.index') }}" class="{{ $link(request()->routeIs('web.buku.*')) }}">Daftar Buku</a>@endcan
                        @can('kelola-peminjaman')<a href="{{ route('peminjaman.index') }}" class="{{ $link(request()->routeIs('peminjaman.index')) }}">Peminjaman</a>@endcan
                        @can('kelola-pengembalian')<a href="{{ route('pengembalian.index') }}" class="{{ $link(request()->routeIs('pengembalian.*')) }}">Pengembalian</a>@endcan
                        @can('lihat-laporan-perpustakaan')<a href="{{ route('laporan.index') }}" class="{{ $link(request()->routeIs('laporan.*')) }}">Laporan</a>@endcan
                        @can('kelola-data-pengguna')<a href="{{ route('admin.users.index') }}" class="{{ $link(request()->routeIs('admin.users.*')) }}">Pengguna</a>@endcan
                        @can('melihat-status-peminjaman-sendiri')<a href="{{ route('peminjaman.saya') }}" class="{{ $link(request()->routeIs('peminjaman.saya')) }}">Peminjaman Saya</a>@endcan
                        @can('melihat-riwayat-peminjaman-sendiri')<a href="{{ route('riwayat.saya') }}" class="{{ $link(request()->routeIs('riwayat.saya')) }}">Riwayat</a>@endcan
                    @endauth
                </div>
            </div>

            {{-- Kanan: akun / login --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }} <span class="text-xs text-gray-400">({{ Auth::user()->namaRole() }})</span></div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @can('lihat-profil-sendiri')
                                <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                            @endcan

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 me-4">Login</a>
                    <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900">Registrasi</a>
                @endauth
            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
            <div class="pt-2 pb-3 space-y-1">
                @can('akses-dashboard-admin')<a href="{{ route('admin.dashboard') }}" class="{{ $linkM(request()->routeIs('admin.dashboard')) }}">Dashboard Admin</a>@endcan
                @can('akses-dashboard-petugas')<a href="{{ route('petugas.dashboard') }}" class="{{ $linkM(request()->routeIs('petugas.dashboard')) }}">Dashboard Petugas</a>@endcan
                @can('lihat-daftar-buku')<a href="{{ route('web.buku.index') }}" class="{{ $linkM(request()->routeIs('web.buku.*')) }}">Daftar Buku</a>@endcan
                @can('kelola-peminjaman')<a href="{{ route('peminjaman.index') }}" class="{{ $linkM(request()->routeIs('peminjaman.index')) }}">Peminjaman</a>@endcan
                @can('kelola-pengembalian')<a href="{{ route('pengembalian.index') }}" class="{{ $linkM(request()->routeIs('pengembalian.*')) }}">Pengembalian</a>@endcan
                @can('lihat-laporan-perpustakaan')<a href="{{ route('laporan.index') }}" class="{{ $linkM(request()->routeIs('laporan.*')) }}">Laporan</a>@endcan
                @can('kelola-data-pengguna')<a href="{{ route('admin.users.index') }}" class="{{ $linkM(request()->routeIs('admin.users.*')) }}">Pengguna</a>@endcan
                @can('melihat-status-peminjaman-sendiri')<a href="{{ route('peminjaman.saya') }}" class="{{ $linkM(request()->routeIs('peminjaman.saya')) }}">Peminjaman Saya</a>@endcan
                @can('melihat-riwayat-peminjaman-sendiri')<a href="{{ route('riwayat.saya') }}" class="{{ $linkM(request()->routeIs('riwayat.saya')) }}">Riwayat</a>@endcan
            </div>

            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    @can('lihat-profil-sendiri')
                        <x-responsive-nav-link :href="route('profile.edit')">Profil Saya</x-responsive-nav-link>
                    @endcan
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            Logout
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('login') }}" class="{{ $linkM(false) }}">Login</a>
                <a href="{{ route('register') }}" class="{{ $linkM(false) }}">Registrasi</a>
            </div>
        @endauth
    </div>
</nav>

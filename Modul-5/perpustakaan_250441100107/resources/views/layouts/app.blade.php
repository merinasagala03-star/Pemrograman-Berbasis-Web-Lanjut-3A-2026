{{--
    Layout utama. Dipakai dengan DUA cara:
      1) <x-app-layout> ... </x-app-layout>   (view Breeze: profil)   -> memakai $slot
      2) @extends('layouts.app') + @section('content')  (view perpustakaan) -> memakai @yield
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') — @endif{{ config('app.name', 'Perpustakaan Digital') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                @isset($slot)
                    {{ $slot }}
                @else
                    <div class="halaman">
                        {{-- Pesan flash (sukses / gagal) dari controller --}}
                        @if (session('status') && ! in_array(session('status'), ['profile-updated', 'password-updated', 'verification-link-sent'], true))
                            <div class="pesan pesan--sukses" role="status">{{ session('status') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="pesan pesan--error" role="alert">{{ session('error') }}</div>
                        @endif

                        @yield('content')
                    </div>
                @endisset
            </main>
        </div>
    </body>
</html>

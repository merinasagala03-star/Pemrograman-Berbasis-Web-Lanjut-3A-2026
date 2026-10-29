@props(['judul', 'penulis', 'tahun'])

<article class="kartu-buku">
    <h3 class="kartu-buku__judul">{{ $judul }}</h3>
    <p class="kartu-buku__penulis">{{ $penulis }}</p>
    <span class="kartu-buku__tahun">{{ $tahun }}</span>

    @isset($kategori)
        <span class="kartu-buku__kategori">{{ $kategori }}</span>
    @endisset

    <div class="kartu-buku__aksi">
        {{ $slot }}
    </div>
</article>


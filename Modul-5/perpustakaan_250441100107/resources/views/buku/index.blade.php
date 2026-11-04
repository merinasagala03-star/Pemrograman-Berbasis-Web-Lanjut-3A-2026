@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <h2>Daftar Buku</h2>
    <p class="section-note" id="total-buku">Memuat data…</p>

    {{-- Pesan sukses / gagal untuk semua operasi (GET, POST, PUT, DELETE) --}}
    <div id="pesan" class="pesan" role="status" hidden></div>

    {{-- Formulir tambah / ubah: hanya admin & petugas (permission kelola-data-buku) --}}
    @can('kelola-data-buku')
    <form id="form-buku" class="form-buku" novalidate>
        <h3 id="judul-form">Tambah Buku</h3>

        <div class="form-buku__grid">
            <div class="form-buku__field">
                <label for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id">
                    <option value="">— Pilih kategori —</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                    @endforeach
                </select>
                <small class="form-buku__error" data-error-for="kategori_id"></small>
            </div>

            <div class="form-buku__field">
                <label for="judul">Judul</label>
                <input type="text" id="judul" name="judul" maxlength="150">
                <small class="form-buku__error" data-error-for="judul"></small>
            </div>

            <div class="form-buku__field">
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" maxlength="100">
                <small class="form-buku__error" data-error-for="penulis"></small>
            </div>

            <div class="form-buku__field">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" min="1901" max="{{ date('Y') }}">
                <small class="form-buku__error" data-error-for="tahun_terbit"></small>
            </div>
        </div>

        <div class="form-buku__aksi">
            <button type="submit" class="btn-primary" id="btn-simpan">Simpan</button>
            <button type="button" class="btn-sekunder" id="btn-batal" hidden>Batal</button>
        </div>
    </form>
    @endcan

    {{-- Tabel buku --}}
    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun Terbit</th>
                    @can('kelola-data-buku')<th>Aksi</th>@else<th>Detail</th>@endcan
                </tr>
            </thead>
            <tbody id="tbody-buku">
                <tr><td colspan="6" class="tabel-buku__info">Memuat data…</td></tr>
            </tbody>
        </table>
    </div>

    <div class="paginasi" id="paginasi" hidden>
        <button type="button" class="btn-sekunder" id="btn-prev">« Sebelumnya</button>
        <span id="info-halaman"></span>
        <button type="button" class="btn-sekunder" id="btn-next">Berikutnya »</button>
    </div>

    <script>
        const API_BUKU = '/api/buku';
        // Menentukan apakah tombol Ubah/Hapus ditampilkan. Ini hanya tampilan;
        // server tetap menolak (403) jika tidak punya permission kelola-data-buku.
        const BISA_KELOLA = @json(auth()->user()->can('kelola-data-buku'));
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const FIELD = ['kategori_id', 'judul', 'penulis', 'tahun_terbit'];

        let halaman = 1;        // halaman tabel yang sedang tampil
        let lastPage = 1;
        let idDiubah = null;    // null = mode tambah, angka = mode ubah
        let daftarBuku = [];    // data buku pada halaman aktif

        const el = (id) => document.getElementById(id);
        const form = el('form-buku');   // null jika tidak punya hak kelola

        // ---------- Pesan & error ----------
        function tampilPesan(teks, jenis = 'sukses') {
            const p = el('pesan');
            p.textContent = teks;
            p.className = 'pesan pesan--' + jenis;
            p.hidden = false;
        }

        function bersihkanPesan() {
            el('pesan').hidden = true;
            document.querySelectorAll('[data-error-for]').forEach((s) => (s.textContent = ''));
        }

        function tampilErrorField(errors) {
            Object.entries(errors).forEach(([nama, pesan]) => {
                const s = document.querySelector(`[data-error-for="${nama}"]`);
                if (s) s.textContent = pesan[0];
            });
        }

        // ---------- Pembungkus Fetch API ----------
        // Mengembalikan body JSON jika response.ok, selain itu melempar Error
        // dengan pesan yang mudah dipahami (dan .errors untuk validasi 422).
        async function panggilApi(url, opsi = {}) {
            let response;
            try {
                response = await fetch(url, {
                    ...opsi,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        ...(opsi.headers || {}),
                    },
                });
            } catch (e) {
                // jaringan putus / server mati
                throw new Error('API tidak dapat diakses. Periksa koneksi atau pastikan server berjalan.');
            }

            let body = null;
            try { body = await response.json(); } catch (e) { /* body bukan JSON */ }

            if (!response.ok) {
                let pesan;
                if (response.status === 422) pesan = 'Data yang dikirim tidak valid. Periksa isian formulir.';
                else if (response.status === 401) pesan = 'Sesi login berakhir. Silakan login kembali.';
                else if (response.status === 403) pesan = 'Anda tidak memiliki hak akses untuk operasi ini.';
                else if (response.status === 419) pesan = 'Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.';
                else if (response.status === 404) pesan = (body && body.message) || 'Data tidak ditemukan.';
                else if (response.status >= 500) pesan = 'Terjadi kesalahan pada server. Coba lagi nanti.';
                else pesan = (body && body.message) || `Permintaan gagal (status ${response.status}).`;

                const err = new Error(pesan);
                err.status = response.status;
                err.errors = body && body.errors ? body.errors : null;
                throw err;
            }
            return body;
        }

        // ---------- 1. GET: ambil & tampilkan data ----------
        async function muatBuku(page = halaman) {
            const tbody = el('tbody-buku');
            try {
                const hasil = await panggilApi(`${API_BUKU}?page=${page}`);

                // jika halaman terakhir kosong setelah hapus, mundur satu halaman
                if (hasil.data.length === 0 && page > 1) return muatBuku(page - 1);

                halaman = hasil.meta.current_page;
                lastPage = hasil.meta.last_page;
                daftarBuku = hasil.data;

                el('total-buku').textContent = `${hasil.meta.total} judul tersedia di katalog.`;
                renderTabel(hasil.data, hasil.meta.from || 1);
                renderPaginasi();
            } catch (err) {
                el('total-buku').textContent = 'Data buku gagal dimuat.';
                tbody.replaceChildren(barisInfo(err.message, true));
                el('paginasi').hidden = true;
                tampilPesan(err.message, 'error');
            }
        }

        function barisInfo(teks, denganUlang = false) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 6;
            td.className = 'tabel-buku__info';
            td.textContent = teks + ' ';
            if (denganUlang) {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'btn-sekunder';
                b.textContent = 'Coba lagi';
                b.addEventListener('click', () => muatBuku());
                td.appendChild(b);
            }
            tr.appendChild(td);
            return tr;
        }

        function renderTabel(data, mulai) {
            const tbody = el('tbody-buku');
            if (data.length === 0) {
                tbody.replaceChildren(barisInfo('Belum ada data buku.'));
                return;
            }

            const baris = data.map((b, i) => {
                const tr = document.createElement('tr');
                // atribut respons API Resource: kategori, judul, penulis, tahun
                [mulai + i, b.kategori ?? '-', b.judul, b.penulis, b.tahun].forEach((nilai) => {
                    const td = document.createElement('td');
                    td.textContent = nilai;   // textContent: aman dari XSS
                    tr.appendChild(td);
                });

                const aksi = document.createElement('td');
                aksi.className = 'tabel-buku__aksi';

                const detail = document.createElement('a');
                detail.href = `/buku/${b.id}`;
                detail.className = 'btn-ubah';
                detail.textContent = 'Detail';
                aksi.append(detail);

                if (BISA_KELOLA) {
                    const ubah = document.createElement('button');
                    ubah.type = 'button';
                    ubah.className = 'btn-ubah';
                    ubah.textContent = 'Ubah';
                    ubah.addEventListener('click', () => mulaiUbah(b.id));

                    const hapus = document.createElement('button');
                    hapus.type = 'button';
                    hapus.className = 'btn-hapus';
                    hapus.textContent = 'Hapus';
                    hapus.addEventListener('click', () => hapusBuku(b));

                    aksi.append(ubah, hapus);
                }
                tr.appendChild(aksi);
                return tr;
            });
            tbody.replaceChildren(...baris);
        }

        function renderPaginasi() {
            el('paginasi').hidden = lastPage <= 1;
            el('info-halaman').textContent = `Halaman ${halaman} dari ${lastPage}`;
            el('btn-prev').disabled = halaman <= 1;
            el('btn-next').disabled = halaman >= lastPage;
        }

        el('btn-prev').addEventListener('click', () => muatBuku(halaman - 1));
        el('btn-next').addEventListener('click', () => muatBuku(halaman + 1));

        // ---------- 2 & 3. POST (tambah) dan PUT (ubah) ----------
        if (form) form.addEventListener('submit', async (e) => {
            e.preventDefault();
            bersihkanPesan();

            const payload = {};
            FIELD.forEach((f) => (payload[f] = form.elements[f].value));

            const mengubah = idDiubah !== null;
            const url = mengubah ? `${API_BUKU}/${idDiubah}` : API_BUKU;
            const btn = el('btn-simpan');
            btn.disabled = true;

            try {
                await panggilApi(url, {
                    method: mengubah ? 'PUT' : 'POST',
                    body: JSON.stringify(payload),
                });

                tampilPesan(mengubah ? 'Buku berhasil diperbarui.' : 'Buku berhasil ditambahkan.');
                resetForm();
                // buku baru tampil paling atas (halaman 1); hasil ubah tetap di halaman aktif
                await muatBuku(mengubah ? halaman : 1);
            } catch (err) {
                if (err.errors) tampilErrorField(err.errors);
                tampilPesan(err.message, 'error');   // tidak ada pesan sukses palsu
            } finally {
                btn.disabled = false;
            }
        });

        function mulaiUbah(id) {
            const b = daftarBuku.find((x) => x.id === id);
            if (!b) return;

            bersihkanPesan();
            idDiubah = id;
            form.elements['kategori_id'].value = b.kategori_id;
            form.elements['judul'].value = b.judul;
            form.elements['penulis'].value = b.penulis;
            form.elements['tahun_terbit'].value = b.tahun;

            el('judul-form').textContent = 'Ubah Buku';
            el('btn-simpan').textContent = 'Simpan Perubahan';
            el('btn-batal').hidden = false;
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            form.elements['judul'].focus();
        }

        function resetForm() {
            form.reset();
            idDiubah = null;
            el('judul-form').textContent = 'Tambah Buku';
            el('btn-simpan').textContent = 'Simpan';
            el('btn-batal').hidden = true;
            document.querySelectorAll('[data-error-for]').forEach((s) => (s.textContent = ''));
        }

        if (form) el('btn-batal').addEventListener('click', () => {
            resetForm();
            bersihkanPesan();
        });

        // ---------- 4. DELETE ----------
        async function hapusBuku(b) {
            if (!confirm(`Hapus buku "${b.judul}"?`)) return;   // batal → tidak ada request

            bersihkanPesan();
            try {
                await panggilApi(`${API_BUKU}/${b.id}`, { method: 'DELETE' });
                if (idDiubah === b.id) resetForm();
                tampilPesan('Buku berhasil dihapus.');
                await muatBuku(halaman);
            } catch (err) {
                tampilPesan(err.message, 'error');
                if (err.status === 404) await muatBuku(halaman);   // data sudah tidak ada, sinkronkan tabel
            }
        }

        // ---------- Awal ----------
        muatBuku(1);
    </script>
@endsection

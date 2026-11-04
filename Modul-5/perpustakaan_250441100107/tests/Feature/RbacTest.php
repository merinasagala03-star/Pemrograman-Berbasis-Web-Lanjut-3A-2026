<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Soal 3, 4, 5: RBAC dengan Spatie Laravel Permission.
 * Nomor skenario (S1..S15) mengikuti tabel pengujian pada soal nomor 5.
 */
class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
    }

    private function user(string $role, string $email = null): User
    {
        $email ??= $role.'-'.uniqid().'@example.test';
        $anggota = Anggota::create(['nama' => ucfirst($role), 'email' => $email, 'no_telepon' => '0812']);
        $user = User::create([
            'name' => ucfirst($role), 'email' => $email,
            'password' => Hash::make('password'), 'anggota_id' => $anggota->id,
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function buku(): Buku
    {
        $k = Kategori::firstOrCreate(['nama' => 'Fiksi']);

        return Buku::create(['kategori_id' => $k->id, 'judul' => 'Laskar Pelangi', 'penulis' => 'Andrea Hirata', 'tahun_terbit' => 2005]);
    }

    private function payloadBuku(int $kategoriId, string $judul = 'Buku Baru'): array
    {
        return ['kategori_id' => $kategoriId, 'judul' => $judul, 'penulis' => 'Penulis Uji', 'tahun_terbit' => 2020];
    }

    // ---- Matriks permission (soal 4) ----
    public function test_matriks_permission_sesuai_tabel_soal(): void
    {
        $this->assertSame(16, \Spatie\Permission\Models\Permission::count());
        $this->assertSame(3, \Spatie\Permission\Models\Role::count());

        $admin = $this->user('admin');
        $petugas = $this->user('petugas');
        $siswa = $this->user('siswa');

        $this->assertTrue($admin->can('akses-dashboard-admin'));
        $this->assertFalse($petugas->can('akses-dashboard-admin'));
        $this->assertTrue($petugas->can('akses-dashboard-petugas'));
        $this->assertFalse($admin->can('akses-dashboard-petugas'));

        foreach (['kelola-data-pengguna', 'kelola-role-pengguna'] as $p) {
            $this->assertTrue($admin->can($p));
            $this->assertFalse($petugas->can($p));
            $this->assertFalse($siswa->can($p));
        }
        foreach (['kelola-data-buku', 'kelola-peminjaman', 'kelola-pengembalian', 'memproses-persetujuan-peminjaman', 'mencatat-pengembalian-buku', 'lihat-laporan-perpustakaan'] as $p) {
            $this->assertTrue($admin->can($p));
            $this->assertTrue($petugas->can($p));
            $this->assertFalse($siswa->can($p), $p);
        }
        foreach (['lihat-daftar-buku', 'mengajukan-peminjaman-buku', 'melihat-status-peminjaman-sendiri', 'melihat-riwayat-peminjaman-sendiri', 'lihat-profil-sendiri', 'ubah-profil-sendiri'] as $p) {
            $this->assertTrue($admin->can($p));
            $this->assertTrue($petugas->can($p));
            $this->assertTrue($siswa->can($p), $p);
        }
    }

    // ---- S1 ----
    public function test_s1_tamu_membuka_dashboard_admin_dialihkan_ke_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    // ---- S2 ----
    public function test_s2_admin_membuka_dashboard_admin(): void
    {
        $this->actingAs($this->user('admin'))->get('/admin/dashboard')->assertOk()->assertSee('Dashboard Admin');
    }

    // ---- S3 ----
    public function test_s3_admin_menambah_mengubah_menghapus_buku(): void
    {
        $admin = $this->user('admin');
        $k = Kategori::create(['nama' => 'Sains']);

        $res = $this->actingAs($admin)->postJson('/api/buku', $this->payloadBuku($k->id))->assertCreated();
        $id = Buku::firstOrFail()->id;

        $this->actingAs($admin)->putJson("/api/buku/$id", $this->payloadBuku($k->id, 'Judul Diubah'))->assertOk();
        $this->assertDatabaseHas('bukus', ['id' => $id, 'judul' => 'Judul Diubah']);

        $this->actingAs($admin)->deleteJson("/api/buku/$id")->assertOk();
        $this->assertDatabaseMissing('bukus', ['id' => $id]);
    }

    // ---- S4 ----
    public function test_s4_admin_mengelola_akun_dan_role(): void
    {
        $admin = $this->user('admin');
        $target = $this->user('siswa');

        $this->actingAs($admin)->get('/admin/pengguna')->assertOk();
        $this->actingAs($admin)->get('/admin/pengguna/create')->assertOk();

        $this->actingAs($admin)->post('/admin/pengguna', [
            'name' => 'Petugas Baru', 'email' => 'baru@example.test', 'no_telepon' => '0813',
            'password' => 'PasswordKuat1', 'role' => 'petugas',
        ])->assertRedirect(route('admin.users.index'));
        $baru = User::where('email', 'baru@example.test')->firstOrFail();
        $this->assertTrue($baru->hasRole('petugas'));
        $this->assertNotSame('PasswordKuat1', $baru->password);

        $this->actingAs($admin)->put("/admin/pengguna/{$target->id}", [
            'name' => $target->name, 'email' => $target->email, 'no_telepon' => '0812', 'role' => 'petugas',
        ])->assertRedirect(route('admin.users.index'));
        $this->assertTrue($target->fresh()->hasRole('petugas'));
        $this->assertFalse($target->fresh()->hasRole('siswa'));

        $this->actingAs($admin)->delete("/admin/pengguna/{$baru->id}")->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $baru->id]);
    }

    public function test_admin_tidak_bisa_menurunkan_role_atau_menghapus_dirinya_sendiri(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->from('/admin/pengguna')->put("/admin/pengguna/{$admin->id}", [
            'name' => $admin->name, 'email' => $admin->email, 'no_telepon' => '0812', 'role' => 'siswa',
        ])->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->hasRole('admin'));

        $this->actingAs($admin)->delete("/admin/pengguna/{$admin->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    // ---- S5 ----
    public function test_s5_admin_mengakses_peminjaman_pengembalian_dan_laporan(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/peminjaman')->assertOk();
        $this->actingAs($admin)->get('/pengembalian')->assertOk();
        $this->actingAs($admin)->get('/laporan')->assertOk();
        $this->actingAs($admin)->getJson('/api/rekap/buku-per-kategori')->assertOk();
    }

    // ---- S6 ----
    public function test_s6_petugas_membuka_dashboard_petugas_tetapi_bukan_dashboard_admin(): void
    {
        $petugas = $this->user('petugas');

        $this->actingAs($petugas)->get('/petugas/dashboard')->assertOk()->assertSee('Dashboard Petugas');
        $this->actingAs($petugas)->get('/admin/dashboard')->assertForbidden();
    }

    // ---- S7 ----
    public function test_s7_petugas_mengelola_data_buku(): void
    {
        $petugas = $this->user('petugas');
        $k = Kategori::create(['nama' => 'Sains']);

        $this->actingAs($petugas)->postJson('/api/buku', $this->payloadBuku($k->id))->assertCreated();
        $id = Buku::firstOrFail()->id;
        $this->actingAs($petugas)->putJson("/api/buku/$id", $this->payloadBuku($k->id, 'Diubah'))->assertOk();
        $this->actingAs($petugas)->deleteJson("/api/buku/$id")->assertOk();
    }

    // ---- S8 ----
    public function test_s8_petugas_memproses_peminjaman_dan_pengembalian(): void
    {
        $petugas = $this->user('petugas');
        $siswa = $this->user('siswa');
        $buku = $this->buku();

        // siswa mengajukan
        $this->actingAs($siswa)->post("/buku/{$buku->id}/pinjam")->assertRedirect(route('peminjaman.saya'));
        $p = Peminjaman::firstOrFail();
        $this->assertSame('diajukan', $p->status);
        $this->assertSame($siswa->anggota_id, $p->anggota_id);

        // petugas menyetujui
        $this->actingAs($petugas)->get('/peminjaman')->assertOk()->assertSee('Setujui');
        $this->actingAs($petugas)->post("/peminjaman/{$p->id}/setujui")->assertRedirect();
        $this->assertSame('dipinjam', $p->fresh()->status);
        $this->assertSame($petugas->id, $p->fresh()->petugas_id);

        // petugas mencatat pengembalian
        $this->actingAs($petugas)->get('/pengembalian')->assertOk();
        $this->actingAs($petugas)->post("/pengembalian/{$p->id}/kembalikan")->assertRedirect();
        $this->assertSame('dikembalikan', $p->fresh()->status);
        $this->assertNotNull($p->fresh()->tanggal_kembali);
    }

    public function test_petugas_dapat_menolak_pengajuan(): void
    {
        $petugas = $this->user('petugas');
        $siswa = $this->user('siswa');
        $buku = $this->buku();

        $this->actingAs($siswa)->post("/buku/{$buku->id}/pinjam");
        $p = Peminjaman::firstOrFail();
        $this->actingAs($petugas)->post("/peminjaman/{$p->id}/tolak")->assertRedirect();
        $this->assertSame('ditolak', $p->fresh()->status);
    }

    public function test_siswa_tidak_bisa_memproses_persetujuan_atau_pengembalian(): void
    {
        $siswa = $this->user('siswa');
        $buku = $this->buku();
        $p = Peminjaman::create([
            'anggota_id' => $siswa->anggota_id, 'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(), 'status' => 'diajukan',
        ]);

        $this->actingAs($siswa)->post("/peminjaman/{$p->id}/setujui")->assertForbidden();
        $this->actingAs($siswa)->post("/pengembalian/{$p->id}/kembalikan")->assertForbidden();
        $this->actingAs($siswa)->get('/peminjaman')->assertForbidden();
        $this->actingAs($siswa)->get('/pengembalian')->assertForbidden();
        $this->actingAs($siswa)->get('/laporan')->assertForbidden();
        $this->assertSame('diajukan', $p->fresh()->status);
    }

    // ---- S9 ----
    public function test_s9_petugas_tidak_bisa_mengelola_akun_atau_role(): void
    {
        $petugas = $this->user('petugas');
        $target = $this->user('siswa');

        $this->actingAs($petugas)->get('/admin/pengguna')->assertForbidden();
        $this->actingAs($petugas)->get('/admin/pengguna/create')->assertForbidden();
        $this->actingAs($petugas)->get("/admin/pengguna/{$target->id}/edit")->assertForbidden();
        $this->actingAs($petugas)->put("/admin/pengguna/{$target->id}", ['role' => 'admin'])->assertForbidden();
        $this->actingAs($petugas)->delete("/admin/pengguna/{$target->id}")->assertForbidden();
        $this->assertTrue($target->fresh()->hasRole('siswa'));
    }

    // ---- S10 ----
    public function test_s10_siswa_melihat_daftar_dan_detail_buku(): void
    {
        $siswa = $this->user('siswa');
        $buku = $this->buku();

        $this->actingAs($siswa)->get('/buku')->assertOk();
        $this->actingAs($siswa)->get("/buku/{$buku->id}")->assertOk()->assertSee('Laskar Pelangi')->assertSee('Ajukan Peminjaman');
        $this->actingAs($siswa)->getJson('/api/buku')->assertOk()->assertJsonFragment(['judul' => 'Laskar Pelangi']);
    }

    // ---- S11 ----
    public function test_s11_siswa_melihat_dan_mengubah_profil_sendiri(): void
    {
        $siswa = $this->user('siswa');

        $this->actingAs($siswa)->get('/profil')->assertOk()->assertSee($siswa->email);

        $this->actingAs($siswa)->patch('/profil', [
            'name' => 'Nama Baru', 'email' => $siswa->email, 'no_telepon' => '0899',
        ])->assertRedirect(route('profile.edit'));

        $this->assertSame('Nama Baru', $siswa->fresh()->name);
        $this->assertSame('0899', $siswa->fresh()->anggota->no_telepon);
    }

    public function test_profil_selalu_milik_user_login_dan_role_tidak_bisa_diubah_lewat_profil(): void
    {
        $siswa = $this->user('siswa');
        $lain = $this->user('siswa');

        // Mengirim id/role/anggota_id milik orang lain tidak berpengaruh.
        $this->actingAs($siswa)->patch('/profil', [
            'name' => 'Saya', 'email' => $siswa->email, 'no_telepon' => '0811',
            'id' => $lain->id, 'role' => 'admin', 'anggota_id' => $lain->anggota_id,
        ])->assertRedirect();

        $this->assertSame('Saya', $siswa->fresh()->name);
        $this->assertNotSame('Saya', $lain->fresh()->name);
        $this->assertTrue($siswa->fresh()->hasRole('siswa'));
        $this->assertFalse($siswa->fresh()->hasRole('admin'));
        $this->assertSame($siswa->anggota_id, $siswa->fresh()->anggota_id);
    }

    public function test_ganti_password_menyimpan_hash_dan_butuh_password_lama(): void
    {
        $siswa = $this->user('siswa'); // password: "password"

        $this->actingAs($siswa)->put('/profil/password', [
            'current_password' => 'salah', 'password' => 'BaruKuat12345', 'password_confirmation' => 'BaruKuat12345',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($siswa)->put('/profil/password', [
            'current_password' => 'password', 'password' => 'BaruKuat12345', 'password_confirmation' => 'BaruKuat12345',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('BaruKuat12345', $siswa->fresh()->password));
    }

    // ---- S12 ----
    public function test_s12_siswa_melihat_riwayat_sendiri(): void
    {
        $siswa = $this->user('siswa');
        $buku = $this->buku();
        Peminjaman::create(['anggota_id' => $siswa->anggota_id, 'buku_id' => $buku->id, 'tanggal_pinjam' => now()->toDateString(), 'status' => 'dikembalikan', 'tanggal_kembali' => now()->toDateString()]);

        $this->actingAs($siswa)->get('/riwayat-peminjaman')->assertOk()->assertSee('Laskar Pelangi');
        $this->actingAs($siswa)->get("/riwayat-peminjaman/{$siswa->id}")->assertOk();
        $this->actingAs($siswa)->get('/peminjaman-saya')->assertOk();
    }

    // ---- S13 ----
    public function test_s13_siswa_tidak_bisa_membuka_dashboard_admin_atau_kelola_buku(): void
    {
        $siswa = $this->user('siswa');
        $k = Kategori::create(['nama' => 'Sains']);
        $buku = $this->buku();

        $this->actingAs($siswa)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($siswa)->get('/petugas/dashboard')->assertForbidden();
        $this->actingAs($siswa)->get('/admin/pengguna')->assertForbidden();

        // pengelolaan buku (lewat URL/API langsung)
        $this->actingAs($siswa)->postJson('/api/buku', $this->payloadBuku($k->id))->assertForbidden();
        $this->actingAs($siswa)->putJson("/api/buku/{$buku->id}", $this->payloadBuku($k->id, 'Retas'))->assertForbidden();
        $this->actingAs($siswa)->deleteJson("/api/buku/{$buku->id}")->assertForbidden();
        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'judul' => 'Laskar Pelangi']);
        $this->actingAs($siswa)->getJson('/api/rekap/buku-per-kategori')->assertForbidden();
    }

    public function test_siswa_tidak_melihat_form_dan_tombol_kelola_buku(): void
    {
        $this->buku();
        $siswa = $this->user('siswa');
        $petugas = $this->user('petugas');

        $this->actingAs($siswa)->get('/buku')->assertOk()
            ->assertDontSee('id="form-buku"', false)
            ->assertSee('const BISA_KELOLA = false', false);

        $this->actingAs($petugas)->get('/buku')->assertOk()
            ->assertSee('id="form-buku"', false)
            ->assertSee('const BISA_KELOLA = true', false);
    }

    public function test_menu_sesuai_role_dengan_direktif_can(): void
    {
        $this->actingAs($this->user('siswa'))->get('/buku')
            ->assertDontSee('Dashboard Admin')->assertDontSee('Dashboard Petugas')
            ->assertDontSee('>Pengguna<', false)->assertSee('Peminjaman Saya');

        $this->actingAs($this->user('petugas'))->get('/buku')
            ->assertSee('Dashboard Petugas')->assertDontSee('Dashboard Admin')->assertDontSee('>Pengguna<', false);

        $this->actingAs($this->user('admin'))->get('/buku')
            ->assertSee('Dashboard Admin')->assertSee('>Pengguna<', false);
    }

    // ---- S14 ----
    public function test_s14_siswa_tidak_bisa_melihat_riwayat_siswa_lain(): void
    {
        $siswa1 = $this->user('siswa');
        $siswa2 = $this->user('siswa');
        $buku = $this->buku();
        Peminjaman::create(['anggota_id' => $siswa2->anggota_id, 'buku_id' => $buku->id, 'tanggal_pinjam' => now()->toDateString(), 'status' => 'dipinjam']);

        $this->actingAs($siswa1)->get("/riwayat-peminjaman/{$siswa2->id}")->assertForbidden();

        // Riwayat sendiri tidak memuat data siswa lain
        $this->actingAs($siswa1)->get('/riwayat-peminjaman')->assertOk()->assertDontSee('Laskar Pelangi');
        $this->actingAs($siswa1)->get('/peminjaman-saya')->assertOk()->assertDontSee('Laskar Pelangi');
    }

    public function test_staf_boleh_melihat_riwayat_pengguna_lain(): void
    {
        $siswa = $this->user('siswa');
        $this->actingAs($this->user('petugas'))->get("/riwayat-peminjaman/{$siswa->id}")->assertOk();
        $this->actingAs($this->user('admin'))->get("/riwayat-peminjaman/{$siswa->id}")->assertOk();
    }

    public function test_pengajuan_peminjaman_tidak_bisa_atas_nama_orang_lain(): void
    {
        $siswa1 = $this->user('siswa');
        $siswa2 = $this->user('siswa');
        $buku = $this->buku();

        $this->actingAs($siswa1)->post("/buku/{$buku->id}/pinjam", ['anggota_id' => $siswa2->anggota_id, 'status' => 'dipinjam']);

        $p = Peminjaman::firstOrFail();
        $this->assertSame($siswa1->anggota_id, $p->anggota_id);
        $this->assertSame('diajukan', $p->status);
    }

    public function test_tidak_bisa_mengajukan_buku_yang_sama_dua_kali(): void
    {
        $siswa = $this->user('siswa');
        $buku = $this->buku();

        $this->actingAs($siswa)->post("/buku/{$buku->id}/pinjam");
        $this->actingAs($siswa)->post("/buku/{$buku->id}/pinjam")->assertSessionHas('error');
        $this->assertSame(1, Peminjaman::count());
    }

    // ---- S15 ----
    public function test_s15_logout_lalu_halaman_terlindungi_butuh_login_kembali(): void
    {
        foreach (['admin', 'petugas', 'siswa'] as $role) {
            $user = $this->user($role);
            $this->actingAs($user)->get('/buku')->assertOk();
            $this->post('/logout');
            $this->assertGuest();
            $this->get('/buku')->assertRedirect(route('login'));
            $this->get('/profil')->assertRedirect(route('login'));
        }
    }

    public function test_api_tanpa_login_ditolak_401(): void
    {
        $this->getJson('/api/buku')->assertUnauthorized();
        $this->postJson('/api/buku', [])->assertUnauthorized();
    }

    public function test_dashboard_mengarahkan_sesuai_role(): void
    {
        $this->actingAs($this->user('admin'))->get('/dashboard')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->user('petugas'))->get('/dashboard')->assertRedirect(route('petugas.dashboard'));
        $this->actingAs($this->user('siswa'))->get('/dashboard')->assertRedirect(route('web.buku.index'));
    }

    public function test_user_tanpa_role_tidak_punya_akses_apa_pun(): void
    {
        $tanpaRole = User::factory()->create();

        $this->actingAs($tanpaRole)->get('/buku')->assertForbidden();
        $this->actingAs($tanpaRole)->get('/dashboard')->assertForbidden();
        $this->actingAs($tanpaRole)->get('/profil')->assertForbidden();
    }
}

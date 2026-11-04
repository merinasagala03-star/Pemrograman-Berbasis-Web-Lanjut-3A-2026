<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Soal 1 & 2: registrasi, login, logout, hashing, middleware auth & guest. */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
    }

    private function dataRegistrasi(array $override = []): array
    {
        return array_merge([
            'name' => 'Budi Siswa',
            'email' => 'budi@example.test',
            'no_telepon' => '081234567890',
            'password' => 'RahasiaKuat123',
            'password_confirmation' => 'RahasiaKuat123',
        ], $override);
    }

    public function test_halaman_login_dan_registrasi_tampil_untuk_tamu(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_registrasi_berhasil_membuat_user_siswa_dengan_password_ter_hash(): void
    {
        $this->post('/register', $this->dataRegistrasi())->assertRedirect(route('dashboard'));

        $user = User::where('email', 'budi@example.test')->firstOrFail();
        $this->assertNotSame('RahasiaKuat123', $user->password);
        $this->assertTrue(Hash::check('RahasiaKuat123', $user->password));
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertNotNull($user->anggota_id);
        $this->assertDatabaseHas('anggotas', ['email' => 'budi@example.test']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registrasi_tidak_bisa_memilih_role_sendiri(): void
    {
        $this->post('/register', $this->dataRegistrasi(['role' => 'admin']));

        $user = User::where('email', 'budi@example.test')->firstOrFail();
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertFalse($user->hasRole('admin'));
    }

    public function test_registrasi_ditolak_jika_data_tidak_valid(): void
    {
        $this->post('/register', $this->dataRegistrasi(['password_confirmation' => 'beda']))
            ->assertSessionHasErrors('password');

        User::factory()->create(['email' => 'dobel@example.test']);
        $this->post('/register', $this->dataRegistrasi(['email' => 'dobel@example.test']))
            ->assertSessionHasErrors('email');

        Anggota::create(['nama' => 'X', 'email' => 'anggota@example.test', 'no_telepon' => '0']);
        $this->post('/register', $this->dataRegistrasi(['email' => 'anggota@example.test']))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create(['password' => Hash::make('rahasia123')]);
        $user->assignRole('siswa');

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_dengan_kredensial_tidak_valid_ditolak(): void
    {
        $user = User::factory()->create(['password' => Hash::make('rahasia123')]);

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => 'tidakada@example.test', 'password' => 'x'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_halaman_terlindungi_mengalihkan_tamu_ke_login(): void
    {
        foreach (['/dashboard', '/buku', '/profil', '/peminjaman-saya', '/riwayat-peminjaman', '/admin/dashboard'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_middleware_guest_mengalihkan_user_login_dari_login_dan_registrasi(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $this->actingAs($user)->get('/login')->assertRedirect('/dashboard');
        $this->actingAs($user)->get('/register')->assertRedirect('/dashboard');
    }

    public function test_logout_lalu_harus_login_kembali(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $this->actingAs($user)->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();

        $this->get('/buku')->assertRedirect(route('login'));
    }
}

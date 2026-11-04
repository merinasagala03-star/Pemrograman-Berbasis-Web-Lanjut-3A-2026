<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Pengelolaan akun & role. Seluruh route dilindungi permission:kelola-data-pengguna.
 * Penetapan role ADALAH jalur berwenang satu-satunya (selain seeder) dan
 * mensyaratkan permission kelola-role-pengguna.
 */
class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User, 'roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'unique:anggotas,email'],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['required', Password::defaults()],
            'role' => ['nullable', Rule::exists('roles', 'name')],
        ]);

        // Hanya yang berhak mengelola role yang boleh memilih role; selain itu default "siswa".
        $role = $request->user()->can('kelola-role-pengguna') ? ($data['role'] ?? 'siswa') : 'siswa';

        DB::transaction(function () use ($data, $role) {
            $anggota = Anggota::create([
                'nama' => $data['name'], 'email' => $data['email'], 'no_telepon' => $data['no_telepon'],
            ]);
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'anggota_id' => $anggota->id,
            ]);
            $user->syncRoles([$role]);
        });

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user->load('anggota'), 'roles' => Role::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $anggota = $user->pastikanAnggota();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('anggotas', 'email')->ignore($anggota->id),
            ],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['nullable', Password::defaults()],
            'role' => ['nullable', Rule::exists('roles', 'name')],
        ]);

        $bolehUbahRole = $request->user()->can('kelola-role-pengguna');

        // Admin tidak boleh menurunkan role dirinya sendiri (mencegah kehilangan akses admin).
        if ($bolehUbahRole && $user->is($request->user()) && ($data['role'] ?? 'admin') !== 'admin') {
            return back()->withInput()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        DB::transaction(function () use ($data, $user, $anggota, $bolehUbahRole) {
            $user->fill(['name' => $data['name'], 'email' => $data['email']]);
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->save();

            $anggota->update([
                'nama' => $data['name'], 'email' => $data['email'], 'no_telepon' => $data['no_telepon'],
            ]);

            if ($bolehUbahRole && ! empty($data['role'])) {
                $user->syncRoles([$data['role']]);
            }
        });

        return redirect()->route('admin.users.index')->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil dihapus.');
    }
}

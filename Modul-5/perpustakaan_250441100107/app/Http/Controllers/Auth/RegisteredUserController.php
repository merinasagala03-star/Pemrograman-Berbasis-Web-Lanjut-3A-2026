<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Registrasi mandiri.
     *
     * - Membuat baris `anggotas` + `users` yang saling terhubung (anggota_id).
     * - Password disimpan ter-hash (Hash::make, ditambah cast 'hashed' pada model).
     * - Role SELALU "siswa". Input `role` dari request sengaja diabaikan,
     *   sehingga pengguna tidak bisa memilih role sendiri (Soal 3 & 4).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'unique:anggotas,email'],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($data) {
            $anggota = Anggota::create([
                'nama' => $data['name'],
                'email' => $data['email'],
                'no_telepon' => $data['no_telepon'],
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'anggota_id' => $anggota->id,
            ]);

            $user->assignRole('siswa');

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

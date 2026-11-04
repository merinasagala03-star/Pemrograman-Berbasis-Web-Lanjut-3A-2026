<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Profil SELALU milik user yang sedang login ($request->user()).
 * Tidak ada parameter id pada route, jadi tidak mungkin membuka/mengubah
 * profil orang lain (Soal 4: akses data pribadi dicek berdasarkan identitas).
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->pastikanAnggota();

        return view('profile.edit', ['user' => $user->load('anggota')]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $anggota = $user->pastikanAnggota();
        $data = $request->validated();

        DB::transaction(function () use ($user, $anggota, $data) {
            $user->fill(['name' => $data['name'], 'email' => $data['email']]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();

            // Data anggota ikut diperbarui agar akun & data anggota tetap sinkron.
            $anggota->update([
                'nama' => $data['name'],
                'email' => $data['email'],
                'no_telepon' => $data['no_telepon'],
            ]);
        });

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}

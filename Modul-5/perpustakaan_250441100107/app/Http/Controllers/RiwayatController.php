<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    /** Riwayat milik sendiri. */
    public function saya(Request $request): View
    {
        return $this->tampil($request->user(), true);
    }

    /**
     * Riwayat milik user lain: dicek lewat UserPolicy::viewRiwayat.
     * Siswa -> 403 bila $user bukan dirinya. Admin/petugas -> boleh.
     */
    public function show(User $user): View
    {
        Gate::authorize('viewRiwayat', $user);

        return $this->tampil($user, auth()->id() === $user->id);
    }

    private function tampil(User $pemilik, bool $milikSendiri): View
    {
        $peminjamans = $pemilik->peminjamanSaya()->with('buku')->latest('id')->paginate(15);

        return view('peminjaman.riwayat', compact('pemilik', 'peminjamans', 'milikSendiri'));
    }
}

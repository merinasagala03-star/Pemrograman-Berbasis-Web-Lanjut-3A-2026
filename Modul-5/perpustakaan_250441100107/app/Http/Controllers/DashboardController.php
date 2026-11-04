<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /** /dashboard: arahkan pengguna ke halaman utama sesuai role-nya. */
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->can('akses-dashboard-admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->can('akses-dashboard-petugas')) {
            return redirect()->route('petugas.dashboard');
        }
        if ($user->can('lihat-daftar-buku')) {
            return redirect()->route('web.buku.index');
        }

        abort(403, 'Akun Anda belum memiliki role.');
    }

    public function admin(): View
    {
        $penggunaPerRole = Role::withCount('users')->orderBy('name')->pluck('users_count', 'name');

        return view('dashboard.admin', [
            'totalBuku' => Buku::count(),
            'totalPengguna' => User::count(),
            'penggunaPerRole' => $penggunaPerRole,
            'diajukan' => Peminjaman::where('status', Peminjaman::DIAJUKAN)->count(),
            'dipinjam' => Peminjaman::where('status', Peminjaman::DIPINJAM)->count(),
            'dikembalikan' => Peminjaman::where('status', Peminjaman::DIKEMBALIKAN)->count(),
        ]);
    }

    public function petugas(): View
    {
        return view('dashboard.petugas', [
            'totalBuku' => Buku::count(),
            'diajukan' => Peminjaman::where('status', Peminjaman::DIAJUKAN)->count(),
            'dipinjam' => Peminjaman::where('status', Peminjaman::DIPINJAM)->count(),
        ]);
    }
}

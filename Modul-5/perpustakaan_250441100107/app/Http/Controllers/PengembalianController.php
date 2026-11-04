<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    /** Buku yang sedang dipinjam & riwayat pengembalian (staf). */
    public function index(): View
    {
        $dipinjam = Peminjaman::with(['anggota', 'buku'])
            ->where('status', Peminjaman::DIPINJAM)
            ->orderBy('tanggal_pinjam')
            ->get();

        $selesai = Peminjaman::with(['anggota', 'buku'])
            ->where('status', Peminjaman::DIKEMBALIKAN)
            ->latest('tanggal_kembali')
            ->limit(15)
            ->get();

        return view('pengembalian.index', compact('dipinjam', 'selesai'));
    }

    public function kembalikan(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::DIPINJAM) {
            return back()->with('error', 'Hanya buku berstatus "dipinjam" yang dapat dicatat pengembaliannya.');
        }

        $peminjaman->update([
            'status' => Peminjaman::DIKEMBALIKAN,
            'tanggal_kembali' => now()->toDateString(),
            'petugas_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Pengembalian buku berhasil dicatat.');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alur peminjaman: diajukan -> dipinjam (disetujui petugas) -> dikembalikan,
     * atau diajukan -> ditolak.
     */
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('status', 20)->default('diajukan')->after('tanggal_kembali');
            $table->foreignId('petugas_id')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();
        });

        // Data lama (sebelum ada alur persetujuan) disesuaikan dengan tanggal_kembali.
        DB::table('peminjamans')->whereNotNull('tanggal_kembali')->update(['status' => 'dikembalikan']);
        DB::table('peminjamans')->whereNull('tanggal_kembali')->update(['status' => 'dipinjam']);
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('petugas_id');
            $table->dropColumn('status');
        });
    }
};

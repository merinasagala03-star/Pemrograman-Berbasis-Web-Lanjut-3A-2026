<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hubungkan akun login (users) dengan data anggota perpustakaan (anggotas).
     * Relasi ini dipakai untuk menentukan peminjaman milik siapa.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('anggota_id')
                ->nullable()
                ->after('email')
                ->unique()
                ->constrained('anggotas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anggota_id');
        });
    }
};

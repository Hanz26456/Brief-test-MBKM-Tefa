<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrations - tambah kolom role di table users
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role: admin atau organizer
            // Default: organizer (penyelenggara event biasa)
            $table->enum('role', ['admin', 'organizer'])
                   ->default('organizer')
                   ->after('password'); // Letaknya setelah kolom password
        });
    }

    /**
     * Rollback migrations - hapus kolom role
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
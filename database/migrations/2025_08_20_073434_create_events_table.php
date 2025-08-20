<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrations - membuat table events
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();                                    // ID unik auto increment
            $table->string('title');                         // Judul event (contoh: "Tech Conference 2025")
            $table->text('description');                     // Deskripsi lengkap event
            $table->string('venue');                         // Tempat event (contoh: "Hall A, Polije")
            $table->dateTime('start_datetime');              // Tanggal & waktu mulai
            $table->dateTime('end_datetime');                // Tanggal & waktu selesai
            $table->enum('status', ['draft', 'published'])   // Status: draft (belum dipublish) atau published (sudah dipublish)
                   ->default('draft');                       // Default: draft
            $table->foreignId('organizer_id')                // ID organizer (siapa yang buat event ini)
                   ->constrained('users')                    // Terhubung dengan table users
                   ->onDelete('cascade');                    // Kalau user dihapus, event ikut terhapus
            $table->timestamps();                            // created_at dan updated_at otomatis
        });
    }

    /**
     * Rollback migrations - hapus table events
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * Kolom yang bisa diisi via mass assignment
     * Ini untuk keamanan - hanya kolom ini yang boleh diisi sekaligus
     */
    protected $fillable = [
        'title',           // Judul event
        'description',     // Deskripsi event  
        'venue',          // Tempat event
        'start_datetime', // Tanggal mulai
        'end_datetime',   // Tanggal selesai
        'status',         // Status: draft/published
        'organizer_id'    // ID organizer yang buat event ini
    ];

    /**
     * Cast: mengubah tipe data otomatis
     * Tanggal akan otomatis jadi Carbon instance (mudah di-format)
     */
    protected $casts = [
        'start_datetime' => 'datetime',  // Otomatis jadi Carbon
        'end_datetime' => 'datetime',    // Otomatis jadi Carbon
    ];

    /**
     * Relasi: 1 Event punya 1 Organizer
     * Event ini dibuat oleh siapa? (User dengan role organizer)
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
}
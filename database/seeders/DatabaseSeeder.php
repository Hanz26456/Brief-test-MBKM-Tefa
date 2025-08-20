<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;

class DatabaseSeeder extends Seeder
{
    /**
     * Isi database dengan data awal untuk testing
     */
    public function run(): void
    {
        // Buat 1 Admin User
        $admin = User::create([
            'name' => 'Admin Sistem',           // Nama admin
            'email' => 'admin@example.com',     // Email login admin
            'password' => bcrypt('password'),   // Password: "password" (di-encrypt)
            'role' => 'admin'                   // Role: admin (akses penuh)
        ]);

        // Buat Organizer Pertama
        $organizer1 = User::create([
            'name' => 'Budi Santoso',           // Nama organizer
            'email' => 'budi@example.com',      // Email login
            'password' => bcrypt('password'),   // Password: "password"  
            'role' => 'organizer'               // Role: organizer (buat event)
        ]);

        // Buat Organizer Kedua
        $organizer2 = User::create([
            'name' => 'Sari Dewi',              // Nama organizer
            'email' => 'sari@example.com',      // Email login
            'password' => bcrypt('password'),   // Password: "password"
            'role' => 'organizer'               // Role: organizer
        ]);

        // Buat Event Sample untuk Organizer 1 (Budi)
        Event::create([
            'title' => 'Tech Conference 2025',                                // Judul event
            'description' => 'Konferensi teknologi terbesar di Indonesia dengan pembicara terbaik dari dalam dan luar negeri. Akan membahas tren teknologi terkini seperti AI, Blockchain, dan Cloud Computing.',  // Deskripsi lengkap
            'venue' => 'Hall A, Politeknik Negeri Jember',                    // Tempat event
            'start_datetime' => '2025-08-25 09:00:00',                        // Mulai: 25 Agustus 2025, jam 9 pagi
            'end_datetime' => '2025-08-25 17:00:00',                          // Selesai: jam 5 sore
            'status' => 'published',                                          // Status: sudah dipublish (publik bisa lihat)
            'organizer_id' => $organizer1->id                                 // Dibuat oleh Budi
        ]);

        // Buat Event Sample untuk Organizer 2 (Sari) - Masih Draft
        Event::create([
            'title' => 'Workshop Laravel untuk Pemula',                       // Judul event
            'description' => 'Workshop intensif belajar Laravel dari dasar hingga mahir. Peserta akan belajar membuat aplikasi web lengkap dengan Laravel framework.',  // Deskripsi
            'venue' => 'Lab Komputer Politeknik Negeri Jember',              // Tempat
            'start_datetime' => '2025-09-01 08:00:00',                        // Mulai: 1 September 2025, jam 8 pagi  
            'end_datetime' => '2025-09-01 16:00:00',                          // Selesai: jam 4 sore
            'status' => 'draft',                                              // Status: masih draft (belum dipublish)
            'organizer_id' => $organizer2->id                                 // Dibuat oleh Sari
        ]);

        // Buat Event Sample untuk Organizer 1 (Budi) - Event Kedua
        Event::create([
            'title' => 'Seminar Digital Marketing',                           // Judul event  
            'description' => 'Seminar tentang strategi digital marketing terkini untuk meningkatkan bisnis online Anda.',  // Deskripsi
            'venue' => 'Aula Utama Politeknik Negeri Jember',                // Tempat
            'start_datetime' => '2025-09-15 13:00:00',                        // Mulai: 15 September 2025, jam 1 siang
            'end_datetime' => '2025-09-15 17:00:00',                          // Selesai: jam 5 sore  
            'status' => 'published',                                          // Status: sudah dipublish
            'organizer_id' => $organizer1->id                                 // Dibuat oleh Budi
        ]);
    }
}
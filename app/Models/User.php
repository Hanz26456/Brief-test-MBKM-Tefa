<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /**
     * Kolom yang bisa diisi mass assignment
     * 'role' ditambahkan untuk admin/organizer
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role'  // Baru ditambahkan: admin atau organizer
    ];

    /**
     * Kolom yang disembunyikan saat di-serialize (JSON)
     * Password tidak boleh tampil di API response
     */
    protected $hidden = [
        'password', 
        'remember_token'
    ];

    /**
     * Untuk JWT: mengembalikan identifier unik user (biasanya ID)
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Return ID user
    }

    /**
     * Untuk JWT: custom claims (data tambahan di dalam token)
     * Bisa dikosongkan dulu
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relasi: 1 User punya banyak Events
     * Organizer bisa bikin banyak event
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }
}
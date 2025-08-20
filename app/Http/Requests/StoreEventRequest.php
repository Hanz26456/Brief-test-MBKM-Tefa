<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Tentukan apakah user boleh akses request ini
     */
    public function authorize(): bool
    {
        return true; // Izinkan semua user yang sudah login
    }

    /**
     * Aturan validasi untuk create event
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',                    // Judul wajib, max 255 karakter
            'description' => 'required|string',                      // Deskripsi wajib
            'venue' => 'required|string|max:255',                    // Tempat wajib, max 255 karakter
            'start_datetime' => 'required|date|after:now',           // Tanggal mulai wajib, harus di masa depan
            'end_datetime' => 'required|date|after:start_datetime',  // Tanggal selesai wajib, harus setelah start
            'status' => 'sometimes|in:draft,published'               // Status optional, boleh draft atau published
        ];
    }

    /**
     * Pesan error custom (bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul event harus diisi',
            'title.max' => 'Judul event maksimal 255 karakter',
            'description.required' => 'Deskripsi event harus diisi',
            'venue.required' => 'Tempat event harus diisi',
            'start_datetime.required' => 'Tanggal mulai harus diisi',
            'start_datetime.after' => 'Tanggal mulai harus di masa depan',
            'end_datetime.required' => 'Tanggal selesai harus diisi',
            'end_datetime.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'status.in' => 'Status harus draft atau published'
        ];
    }
}
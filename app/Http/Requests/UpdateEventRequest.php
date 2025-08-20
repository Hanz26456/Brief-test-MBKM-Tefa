<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Tentukan apakah user boleh akses request ini
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk update event
     * Semua field optional (boleh diisi sebagian)
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',                   // Optional, tapi kalau diisi harus string
            'description' => 'sometimes|string',                     // Optional
            'venue' => 'sometimes|string|max:255',                   // Optional
            'start_datetime' => 'sometimes|date|after:now',          // Optional, tapi harus masa depan
            'end_datetime' => 'sometimes|date|after:start_datetime', // Optional, tapi harus setelah start
            'status' => 'sometimes|in:draft,published'               // Optional, draft/published only
        ];
    }

    /**
     * Pesan error custom
     */
    public function messages(): array
    {
        return [
            'title.max' => 'Judul event maksimal 255 karakter',
            'start_datetime.after' => 'Tanggal mulai harus di masa depan',
            'end_datetime.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'status.in' => 'Status harus draft atau published'
        ];
    }
}
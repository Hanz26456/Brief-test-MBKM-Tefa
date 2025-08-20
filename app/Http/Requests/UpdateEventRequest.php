<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize()
    {
        $event = $this->route('event'); // Get event from route parameter
        $user = auth('api')->user();
        
        // Admin bisa update semua event, organizer hanya event miliknya
        return $user->role === 'admin' || $event->organizer_id === $user->id;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'venue' => 'sometimes|required|string|max:255',
            'start_datetime' => 'sometimes|required|date|after:now',
            'end_datetime' => 'sometimes|required|date|after:start_datetime',
            'status' => 'sometimes|in:draft,published'
        ];
    }

    public function messages()
    {
        return [
            'start_datetime.after' => 'Start date must be in the future',
            'end_datetime.after' => 'End date must be after start date',
            'status.in' => 'Status must be either draft or published'
        ];
    }
}
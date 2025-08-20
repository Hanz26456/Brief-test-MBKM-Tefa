<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * TAMPILKAN LIST EVENTS (Public + Search/Filter/Pagination)
     * GET /api/events
     */
    public function index(Request $request)
    {
        // Mulai query Event dengan relasi organizer
        $query = Event::with('organizer:id,name,email'); // Eager loading organizer info

        // ===== SEARCH: Cari berdasarkan judul =====
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }

        // ===== FILTER: Filter berdasarkan status =====
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ===== SORTING: Urutkan berdasarkan tanggal mulai =====
        $sortOrder = $request->sort === 'desc' ? 'desc' : 'asc'; // Default: ascending
        $query->orderBy('start_datetime', $sortOrder);

        // ===== RBAC: Role-based filtering =====
        $user = auth('api')->user();
        
        if (!$user) {
            // User tidak login → hanya tampilkan published events
            $query->where('status', 'published');
        } elseif ($user->role === 'organizer') {
            // Organizer login → tampilkan published events + draft milik sendiri
            $query->where(function($q) use ($user) {
                $q->where('status', 'published')
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('organizer_id', $user->id);
                  });
            });
        }
        // Admin bisa lihat semua (tidak ada filter tambahan)

        // ===== PAGINATION =====
        $perPage = $request->per_page ?? 10; // Default 10 per halaman
        $events = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Events retrieved successfully',
            'data' => $events
        ]);
    }

    /**
     * TAMPILKAN DETAIL EVENT (Public)
     * GET /api/events/{id}
     */
    public function show(Event $event)
    {
        // Load relasi organizer untuk detail lengkap
        $event->load('organizer:id,name,email');
        
        return response()->json([
            'success' => true,
            'message' => 'Event retrieved successfully',
            'data' => $event
        ]);
    }

    /**
     * CREATE EVENT BARU (Auth Required)
     * POST /api/events
     */
    public function store(StoreEventRequest $request)
    {
        // Ambil data yang sudah divalidasi
        $validatedData = $request->validated();
        
        // Tambahkan organizer_id dari user yang login
        $validatedData['organizer_id'] = auth('api')->id();
        
        // Set default status jika tidak diisi
        $validatedData['status'] = $validatedData['status'] ?? 'draft';

        // Create event baru
        $event = Event::create($validatedData);

        // Load relasi organizer untuk response
        $event->load('organizer:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event
        ], 201); // HTTP 201 = Created
    }

    /**
     * UPDATE EVENT (Owner/Admin Only)
     * PUT /api/events/{id}
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $user = auth('api')->user();

        // ===== RBAC CHECK: Apakah user boleh update event ini? =====
        if ($user->role !== 'admin' && $event->organizer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Anda hanya bisa mengubah event milik sendiri'
            ], 403); // HTTP 403 = Forbidden
        }

        // Update event dengan data yang divalidasi
        $event->update($request->validated());

        // Refresh data dan load relasi
        $event->refresh()->load('organizer:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    /**
     * DELETE EVENT (Owner/Admin Only)
     * DELETE /api/events/{id}
     */
    public function destroy(Event $event)
    {
        $user = auth('api')->user();

        // ===== RBAC CHECK: Apakah user boleh delete event ini? =====
        if ($user->role !== 'admin' && $event->organizer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Anda hanya bisa menghapus event milik sendiri'
            ], 403);
        }

        // Simpan data event sebelum dihapus (untuk response)
        $deletedEvent = $event->toArray();

        // Hapus event dari database
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully',
            'deleted_event' => $deletedEvent
        ]);
    }
}
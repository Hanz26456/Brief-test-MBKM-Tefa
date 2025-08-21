<?php
// app/Http/Controllers/Api/EventController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * TAMPILKAN LIST EVENTS dengan Redis Caching
     * Cache duration: 30 detik (sesuai brief)
     */
    public function index(Request $request)
    {
        // Generate cache key berdasarkan query parameters
        $cacheKey = $this->generateCacheKey($request);
        
        // Coba ambil dari cache dulu
        $cachedResult = Cache::get($cacheKey);
        
        if ($cachedResult) {
            Log::info('Events served from cache', ['cache_key' => $cacheKey]);
            
            return response()->json([
                'success' => true,
                'message' => 'Events retrieved successfully (cached)',
                'data' => $cachedResult,
                'cached' => true
            ]);
        }

        // Jika tidak ada di cache, query database
        $query = Event::with('organizer:id,name,email');

        // Search by title
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by start_datetime
        $sortOrder = $request->sort === 'desc' ? 'desc' : 'asc';
        $query->orderBy('start_datetime', $sortOrder);

        // RBAC filtering
        $user = auth('api')->user();
        
        if (!$user) {
            $query->where('status', 'published');
        } elseif ($user->role === 'organizer') {
            $query->where(function($q) use ($user) {
                $q->where('status', 'published')
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('organizer_id', $user->id);
                  });
            });
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $events = $query->paginate($perPage);

        // Simpan ke cache selama 30 detik
        Cache::put($cacheKey, $events, 30);
        
        Log::info('Events cached', [
            'cache_key' => $cacheKey,
            'duration' => '30 seconds'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Events retrieved successfully',
            'data' => $events,
            'cached' => false
        ]);
    }

    /**
     * CREATE EVENT dengan Cache Invalidation
     */
    public function store(StoreEventRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['organizer_id'] = auth('api')->id();
        $validatedData['status'] = $validatedData['status'] ?? 'draft';

        $event = Event::create($validatedData);
        $event->load('organizer:id,name,email');

        // Invalidate cache karena ada event baru
        $this->invalidateEventsCache();

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * UPDATE EVENT dengan Cache Invalidation
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $user = auth('api')->user();

        if ($user->role !== 'admin' && $event->organizer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Anda hanya bisa mengubah event milik sendiri'
            ], 403);
        }

        $event->update($request->validated());
        $event->refresh()->load('organizer:id,name,email');

        // Invalidate cache karena event berubah
        $this->invalidateEventsCache();

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    /**
     * DELETE EVENT dengan Cache Invalidation
     */
    public function destroy(Event $event)
    {
        $user = auth('api')->user();

        if ($user->role !== 'admin' && $event->organizer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Anda hanya bisa menghapus event milik sendiri'
            ], 403);
        }

        $deletedEvent = $event->toArray();
        $event->delete();

        // Invalidate cache karena event dihapus
        $this->invalidateEventsCache();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully',
            'deleted_event' => $deletedEvent
        ]);
    }

    /**
     * Generate cache key berdasarkan query parameters
     */
    private function generateCacheKey(Request $request): string
    {
        $user = auth('api')->user();
        $userRole = $user ? $user->role : 'guest';
        $userId = $user ? $user->id : 'anonymous';
        
        $params = [
            'search' => $request->search ?? '',
            'status' => $request->status ?? '',
            'sort' => $request->sort ?? 'asc',
            'page' => $request->page ?? 1,
            'per_page' => $request->per_page ?? 10,
            'user_role' => $userRole,
            'user_id' => $userId
        ];

        return 'events_list_' . md5(serialize($params));
    }

    /**
     * Invalidate semua cache yang berkaitan dengan events
     */
    private function invalidateEventsCache(): void
    {
        // Hapus semua cache yang dimulai dengan 'events_list_'
        $cacheKeys = Cache::getRedis()->keys('*events_list_*');
        
        if (!empty($cacheKeys)) {
            foreach ($cacheKeys as $key) {
                // Remove prefix yang ditambah Laravel
                $cleanKey = str_replace(config('cache.prefix') . ':', '', $key);
                Cache::forget($cleanKey);
            }
            
            Log::info('Events cache invalidated', [
                'cleared_keys' => count($cacheKeys)
            ]);
        }
    }
}
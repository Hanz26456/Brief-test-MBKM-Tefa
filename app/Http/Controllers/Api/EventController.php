<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('organizer');

        // Search by title
        if ($request->search) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Sort by start_datetime
        $sortOrder = $request->sort === 'desc' ? 'desc' : 'asc';
        $query->orderBy('start_datetime', $sortOrder);

        // For public, only show published events
        if (!auth('api')->check() || auth('api')->user()->role !== 'admin') {
            $query->where('status', 'published');
        }

        $perPage = $request->per_page ?? 10;
        $events = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function show(Event $event)
    {
        $event->load('organizer');
        
        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::create([
            ...$request->validated(),
            'organizer_id' => auth('api')->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event->load('organizer')
        ], 201);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        // Check if user can update this event
        if (auth('api')->user()->role !== 'admin' && $event->organizer_id !== auth('api')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You can only update your own events'
            ], 403);
        }

        $event->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event->load('organizer')
        ]);
    }

    public function destroy(Event $event)
    {
        // Check if user can delete this event
        if (auth('api')->user()->role !== 'admin' && $event->organizer_id !== auth('api')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You can only delete your own events'
            ], 403);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}
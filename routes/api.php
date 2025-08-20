<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

// ===== AUTH ROUTES =====
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
         ->middleware('throttle:5,1');           // Rate limit: 5 attempts/menit
    Route::post('/logout', [AuthController::class, 'logout'])
         ->middleware('auth:api');               // Perlu JWT token
    Route::get('/me', [AuthController::class, 'me'])
         ->middleware('auth:api');               // Perlu JWT token
});

// ===== PUBLIC EVENT ROUTES (Tidak perlu login) =====
Route::get('/events', [EventController::class, 'index']);          // List events
Route::get('/events/{event}', [EventController::class, 'show']);   // Detail event

// ===== PROTECTED EVENT ROUTES (Perlu login) =====
Route::middleware(['auth:api'])->group(function () {
    Route::post('/events', [EventController::class, 'store']);       // Create event
    Route::put('/events/{event}', [EventController::class, 'update']); // Update event
    Route::delete('/events/{event}', [EventController::class, 'destroy']); // Delete event
});

// ===== HEALTH CHECK =====
Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'timestamp' => now()]);
});
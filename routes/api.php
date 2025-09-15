<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Log; 

// Route::post('/debug-session', function (Request $request) {
//     Log::info('--- DEBUGGING SESSION ---');
//     Log::info('Request Headers:', $request->headers->all());
//     Log::info('Session ID:', $request->session()->getId());
//     Log::info('Session Data:', $request->session()->all());
//     Log::info('CSRF Token from Session:', $request->session()->token());
//     Log::info('CSRF Token from Header:', $request->header('X-XSRF-TOKEN'));
//     Log::info('--- END DEBUG ---');

//     return response()->json([
//         'message' => 'Debug info logged successfully.',
//         'session_id' => $request->session()->getId(),
//         'has_session' => $request->hasSession(),
//         'session_token' => $request->session()->token(),
//         'header_token' => $request->header('X-XSRF-TOKEN'),
//     ]);
// });


// Public route that Sanctum will make stateful.
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('users', UserController::class);
});
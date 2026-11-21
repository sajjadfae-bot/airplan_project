<?php

use App\Http\Controllers\airplaneController;
use App\Http\Controllers\pilotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - v1
|--------------------------------------------------------------------------
|
| مسیرهای عمومی: ثبت خلبان و ورود (دریافت token)
| مسیرهای محافظت شده: فقط با هدر Authorization: Bearer <token> قابل دسترساند.
|
*/

Route::post('/pilot/store', [pilotController::class, 'store']);
Route::post('/pilot/login', [pilotController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // خلبان
    Route::get('/pilot/me', [pilotController::class, 'me']);
    Route::get('/pilot', [pilotController::class, 'index']);
    Route::get('/pilot/{pilot}/show', [pilotController::class, 'show']);
    Route::put('/pilot/{pilot}/update', [pilotController::class, 'update']);
    Route::delete('/pilot/{pilot}/destroy', [pilotController::class, 'destroy']);

    // ناوگان هواپیما
    Route::get('/airplane', [airplaneController::class, 'index']);
    Route::post('/airplane/store', [airplaneController::class, 'store']);
    Route::get('/airplane/{airplane}/show', [airplaneController::class, 'show']);
    Route::put('/airplane/{airplane}/update', [airplaneController::class, 'update']);
    Route::delete('/airplane/{airplane}/destroy', [airplaneController::class, 'destroy']);
});

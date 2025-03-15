<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\FirebaseService;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/send-notification', function (Request $request, FirebaseService $firebaseService) {
    $request->validate([
        'token' => 'required',
        'title' => 'required',
        'body' => 'required',
    ]);

    $firebaseService->sendPushNotification($request->token, $request->title, $request->body);

    return response()->json(['message' => 'Notification sent successfully']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-code', [AuthController::class, 'verifyCode']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->get('/nearest-delivery', [DeliveryController::class, 'getNearestDelivery']);


Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'index']);
    Route::post('/users', [AdminController::class, 'store']);
    Route::get('/users/{id}', [AdminController::class, 'show']);
    Route::put('/users/{id}', [AdminController::class, 'update']);
    Route::delete('/users/{id}', [AdminController::class, 'destroy']);
    // Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
});

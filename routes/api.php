<?php

use App\Events\NotificationEvent;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JanusController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use RTippin\Janus\Facades\Janus;

Route::controller(AuthController::class)->group(function () {
    Route::post('/user/register', 'register');
    Route::post('/user/login', 'login')->name('login');
});

Route::get('test', function () {
    Janus::debug()->ping();
    dump('It dumps inline for each http call!');
});


Route::prefix('janus')->group(function () {
    Route::get('/session', [JanusController::class, 'createSession']);
    Route::get('/plugin', [JanusController::class, 'attachPlugin']);
    Route::post('/message', [JanusController::class, 'sendMessage']);
    Route::get('/get-list-room', [JanusController::class, 'getRoomList']);
    Route::post('/get-participants-list', [JanusController::class, 'getParticipantList']);
    Route::post('/create-room', [JanusController::class, 'createRoom']);
    Route::post('/delete-room', [JanusController::class, 'deleteRoom']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/me', [AuthController::class, 'getMe']);
        Route::get('/users', [UserController::class, 'getAllUsers']);
        Route::post('/invite', [UserController::class, 'inviteUser']);
        Route::post('/janus/create-room', [JanusController::class, 'createRoom']);
        Route::get('/logout', [AuthController::class, 'logout']);
    });

    /**
     * Notifications Routes
     */
    Route::prefix('notifications')->group(function () {
        Route::get('/me', [NotificationController::class, 'index'])->name('notification.index');
        Route::get('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notification.read');
    });
});


Route::get('test-event', function () {
    event(new NotificationEvent('Oyaji', 1, 'demo', 1));
    return "Event has been sent";
});

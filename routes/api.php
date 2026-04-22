<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Api\CircuitController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminController;

Route::post('/contacts', [ContactController::class, 'apiStore']);

// Admin authentication routes
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout']);
Route::get('/admin/check', [AdminController::class, 'check']);

// Protected admin routes
Route::middleware('admin.auth')->group(function () {
    Route::get('/contacts', [ContactController::class, 'apiIndex']);
    Route::get('/clients', [ContactController::class, 'apiClients']);
    Route::post('/clients', [ContactController::class, 'apiStoreClient']);
    Route::put('/clients/{id}', [ContactController::class, 'apiUpdateClient']);
    Route::delete('/clients/{id}', [ContactController::class, 'apiDeleteClient']);
    Route::post('/contacts/{id}/reply', [ContactController::class, 'apiReplyToContact']);
    Route::patch('/contacts/{id}/status', [ContactController::class, 'apiUpdateContactStatus']);
    Route::delete('/contacts/{id}', [ContactController::class, 'apiDeleteContact']);
    Route::match(['put', 'post'], '/admin/profile', [AdminController::class, 'updateProfile']);
});

// Public reservation routes (clients need to see occupied seats and create reservations)
Route::get('reservations/occupied/{circuit}', [ReservationController::class, 'occupied']);
Route::post('reservations', [ReservationController::class, 'store']); // Clients create reservations

// Public circuit routes (clients need to view circuits)
Route::get('circuits/{circuit}', [CircuitController::class, 'show']);
Route::get('circuits/by-slug/{slug}', [CircuitController::class, 'showBySlug']);

// Protected admin reservation routes
Route::middleware('admin.auth')->group(function () {
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
    Route::put('reservations/{reservation}', [ReservationController::class, 'update']);
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy']);

    // Protected admin circuit routes
    Route::get('circuits', [CircuitController::class, 'index']);
    Route::post('circuits', [CircuitController::class, 'store']);
    Route::put('circuits/{circuit}', [CircuitController::class, 'update']);
    Route::delete('circuits/{circuit}', [CircuitController::class, 'destroy']);
});


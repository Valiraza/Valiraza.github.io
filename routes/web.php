<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CircuitCatalogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact', [ContactController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);

Route::redirect('/admin', '/admin/login');
Route::view('/admin/login', 'admin.login');
Route::get('/compteadmin', [ContactController::class, 'index']);
Route::get('/detailcircuit', function () {
    return view('detailcircuit');
});

Route::get('/pageAT', [App\Http\Controllers\PageATController::class, 'index']);
Route::get('/circuits/{slug}', [CircuitCatalogController::class, 'show'])->name('circuits.catalog');
Route::get('/circuit/{slug}', function ($slug) {
    $circuit = \App\Models\Circuit::where('slug', $slug)->firstOrFail();
    return view('detailcircuit', compact('circuit'));
})->name('circuits.detail');

// API endpoints for contact messages
Route::get('/api/contacts', [ContactController::class, 'apiIndex']);
Route::post('/api/contacts', [ContactController::class, 'apiStore']);

// API endpoints for client CRM




// API endpoint for replying to contacts
Route::post('/api/contacts/{id}/reply', [ContactController::class, 'apiReplyToContact']);

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Auth::routes();

// Public Routes
Route::get('/', function () {
    return view('auth.login');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Task Routes - will be implemented later
    // Route::resource('tasks', App\Http\Controllers\TaskController::class);
});

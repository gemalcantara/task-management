<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Auth::routes();

// Public Routes
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/tasks');
    }
    return view('auth.login');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TaskController::class, 'index']);
    Route::resource('tasks', App\Http\Controllers\TaskController::class);
});

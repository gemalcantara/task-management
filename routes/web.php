<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Auth::routes();

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tasks', TaskController::class);
    // Toggle task status route
    Route::post('tasks/{task}/toggle-status', [TaskController::class, 'toggleStatus'])->name('tasks.toggle-status');
    // Toggle task visibility route
    Route::post('tasks/{task}/toggle-visibility', [TaskController::class, 'toggleVisibility'])->name('tasks.toggle-visibility');
});

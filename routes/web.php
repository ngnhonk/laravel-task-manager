<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [TaskController::class, 'index'])->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Task routes
    Route::resource('tasks', TaskController::class);
    Route::get('/tasks/starred', [TaskController::class, 'starredTasks'])->name('tasks.starred');
    Route::patch('/tasks/{task}/toggle-starred', [TaskController::class, 'toggleStarred'])->name('tasks.toggleStarred');
    Route::get('/tasks/starred', [TaskController::class, 'starredTasks'])->name('tasks.starred');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/chat');
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/user/{id}', [ChatController::class, 'index'])->name('chat.user');
    Route::get('/chat/group/{id}', [ChatController::class, 'index'])->name('chat.group');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    Route::get('/group/create', [GroupController::class, 'index']);
    Route::post('/group/store', [GroupController::class, 'store']);
    Route::delete('/group/{id}', [GroupController::class, 'destroy'])->name('group.destroy');
});

require __DIR__.'/auth.php';
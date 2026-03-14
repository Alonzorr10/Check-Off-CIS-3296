<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/home', function () {
    if (Auth:: check()) {
        $events = Auth::user()->events;
        return view('events', compact('events'));
    }
    return view('home');
}); 

Route::get('/contributions-logged-in', function () {
    return view('contributions-logged-in');
});

Route::get('/contributions', function () {
    return view('contributions');
});

Route::get('/events', function () {
    return view('events');
});

Route::get('/profile', function () {
    return view('profile');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

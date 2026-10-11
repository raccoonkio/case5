<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TripController::class, 'index'])->name('trips.index');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::get('/register', [SessionController::class, 'register'])->name('register');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::post('/register', [SessionController::class, 'storeUser'])->middleware('throttle:5,1')->name('register.store');
});
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::get('/diary', [TripController::class, 'diary'])->name('diary');
    Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
});
Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');

<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MagicLoginController;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Auth/MagicLogin');
    })->middleware('guest')->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::post('/magic-link', [MagicLoginController::class, 'sendMagicLink'])
        ->middleware('guest')
        ->name('magic-link.send');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

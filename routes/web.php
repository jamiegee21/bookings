<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/appointments', function () {
        return view('appointments');
    })->name('appointments');

    Route::get('/booking-history', function () {
        return view('booking-history');
    })->name('booking-history');

    Route::get('/profile-settings', function () {
        return view('profile-settings');
    })->name('profile-settings');

    Route::get('/loyalty-scheme', function () {
        return view('loyalty-scheme');
    })->name('loyalty-scheme');

    Route::get('/book', function () {
        return view('book');
    })->name('book');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

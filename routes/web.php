<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $confirmedBookingsCount = \App\Models\Booking::where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->where('starts_at', '>=', now())
            ->count();

        return view('dashboard', ['confirmedBookingsCount' => $confirmedBookingsCount]);
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

    Route::get('/booking-confirmation', function () {
        return view('booking-confirmation');
    })->name('booking-confirmation');

    Route::get('/book', function () {
        $confirmedBookingsCount = \App\Models\Booking::where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->where('starts_at', '>=', now())
            ->count();

        if ($confirmedBookingsCount >= 3) {
            return redirect()->route('dashboard')->with('error', 'You have reached the maximum of 3 active bookings.');
        }

        return view('book');
    })->name('book');
});

Route::middleware(['auth', 'can.access.today.view'])->group(function () {
    Route::get('/today-view', function () {
        return view('today-view');
    })->name('today-view');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

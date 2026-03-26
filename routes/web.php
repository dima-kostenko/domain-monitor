<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainCheckController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Guest only ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class,    'showLoginForm'])->name('login');
    Route::post('/login',   [LoginController::class,    'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});

// ─── Authenticated ───────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Root → dashboard
    Route::get('/', fn () => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',              [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',            [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password',   [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile',           [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Domains
    Route::resource('domains', DomainController::class);

    // Domain check history (nested, read-only)
    Route::get('domains/{domain}/checks', [DomainCheckController::class, 'index'])
        ->name('domain-checks.index');
});

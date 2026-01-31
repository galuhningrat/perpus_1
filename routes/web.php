<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard routes - redirect based on role
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->hasAdminAccess()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('member.dashboard');
    })->name('dashboard');
});

// Admin routes
Route::middleware(['auth', 'role:super_admin,pustakawan'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/admin.php';
    });

// Member routes
Route::middleware(['auth', 'role:member'])
    ->prefix('member')
    ->name('member.')
    ->group(function () {
        require __DIR__ . '/member.php';
    });

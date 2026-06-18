<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PrizeController;
use App\Http\Controllers\Admin\SpinController;
use Illuminate\Support\Facades\Route;

// Public admin routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login']);
});

// Protected admin routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Prizes management
    Route::resource('prizes', PrizeController::class);
    Route::post('/prizes/{prize}/update-stock', [PrizeController::class, 'updateStock'])->name('prizes.update-stock');

    // Spins history
    Route::get('/spins', [SpinController::class, 'index'])->name('spins.index');
    Route::get('/spins/export/csv', [SpinController::class, 'export'])->name('spins.export');
    Route::get('/spins/{spin}', [SpinController::class, 'show'])->name('spins.show');
});



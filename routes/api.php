<?php

use App\Http\Controllers\Api\PrizeController;
use App\Http\Controllers\Api\SpinController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function (): void {
    Route::get('/prizes', [PrizeController::class, 'index'])->name('api.prizes.index');
    Route::post('/spins', [SpinController::class, 'store'])->name('api.spins.store');
});


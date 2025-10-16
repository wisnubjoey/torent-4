<?php

use App\Modules\Admin\Controllers\Auth\AdminAuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::middleware('guest:admin')->group(function (): void {
            Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])
                ->name('login');

            Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])
                ->name('login.store');
        });

        Route::middleware('auth:admin')->group(function (): void {
            Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
        });
    });

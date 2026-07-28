<?php

use Illuminate\Support\Facades\Route;
use Said\ChangeRequests\Http\Controllers\ChangeRequestController;

Route::prefix(config('change-requests.route.prefix'))
    ->middleware(config('change-requests.route.middleware'))
    ->name('change-requests.')
    ->group(function (): void {
        Route::get('/', [ChangeRequestController::class, 'index'])->name('index');
        Route::post('/', [ChangeRequestController::class, 'store'])->name('store');
        Route::get('{uuid}', [ChangeRequestController::class, 'show'])->name('show');
    });

<?php

use Illuminate\Support\Facades\Route;
use Said\ChangeRequests\Http\Controllers\ChangeRequestCatalogController;
use Said\ChangeRequests\Http\Controllers\ChangeRequestController;
use Said\ChangeRequests\Http\Controllers\ChangeRequestMessageController;
use Said\ChangeRequests\Http\Controllers\ChangeRequestStatusController;

Route::prefix(config('change-requests.route.prefix'))
    ->middleware(config('change-requests.route.middleware'))
    ->name('change-requests.')
    ->group(function (): void {
        // Antes de {uuid}: si no, 'catalogs' se interpreta como un uuid.
        Route::get('catalogs', [ChangeRequestCatalogController::class, 'index'])->name('catalogs');

        Route::get('/', [ChangeRequestController::class, 'index'])->name('index');
        Route::post('/', [ChangeRequestController::class, 'store'])->name('store');

        // Constreñido a un uuid con guiones: además de la seguridad de la
        // ordenación de rutas de arriba, un uuid malformado 404 en el router
        // sin llegar a la BD.
        Route::get('{uuid}', [ChangeRequestController::class, 'show'])
            ->where('uuid', '[0-9a-fA-F-]{36}')
            ->name('show');
        Route::post('{uuid}/messages', [ChangeRequestMessageController::class, 'store'])
            ->where('uuid', '[0-9a-fA-F-]{36}')
            ->name('messages.store');
        Route::patch('{uuid}/status', [ChangeRequestStatusController::class, 'update'])
            ->where('uuid', '[0-9a-fA-F-]{36}')
            ->name('status.update');
    });

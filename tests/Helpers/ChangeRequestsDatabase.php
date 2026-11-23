<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Apunta la conexión del paquete a SQLite en memoria y corre sus migraciones.
 * Nunca toca la BD compartida real.
 */
function useChangeRequestsSqlite(): void
{
    config(['change-requests.connection' => 'change_requests_testing']);
    config(['database.connections.change_requests_testing' => [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]]);

    DB::purge('change_requests_testing');

    Artisan::call('migrate', [
        '--database' => 'change_requests_testing',
        '--path' => realpath(__DIR__ . '/../../database/migrations/change_requests'),
        '--realpath' => true,
        '--force' => true,
    ]);
}

<?php

return [
    /**
     * Nombre de la conexión Laravel. Si la app ya declara una conexión con este
     * nombre en config/database.php, el paquete NO la pisa.
     */
    'connection' => env('CR_CONNECTION', 'change_requests'),

    'database' => [
        'driver' => env('CR_DB_DRIVER', 'mysql'),
        'host' => env('CR_DB_HOST', '127.0.0.1'),
        'port' => env('CR_DB_PORT', '3306'),
        'database' => env('CR_DB_DATABASE', 'orchestrator'),
        'username' => env('CR_DB_USERNAME', 'root'),
        'password' => env('CR_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
    ],

    /**
     * Discriminador de app en la tabla compartida. Sin esto un ticket es
     * irrecuperable: no se sabe qué app lo originó.
     */
    'source_app' => env('CR_SOURCE_APP', 'marketaid'),

    'route' => [
        'prefix' => env('CR_ROUTE_PREFIX', 'web-api/change-requests'),
        'middleware' => ['web', 'auth'],
    ],

    'per_page' => 25,

    /**
     * Directorio cuyos subdirectorios son los slugs válidos de `affectedModule`.
     * Si no existe, el campo acepta cualquier string.
     */
    'modules_path' => base_path('docs/modules'),
];

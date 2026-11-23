<?php

it('registra las seis rutas con el prefijo de la config', function () {
    $routes = collect(app('router')->getRoutes()->getRoutesByName())
        ->filter(fn ($route, $name) => str_starts_with($name, 'change-requests.'));

    expect($routes->keys()->sort()->values()->all())->toBe([
        'change-requests.catalogs',
        'change-requests.index',
        'change-requests.messages.store',
        'change-requests.show',
        'change-requests.status.update',
        'change-requests.store',
    ])->and($routes->every(fn ($route) => str_starts_with($route->uri(), 'web-api/change-requests')))
        ->toBeTrue();
});

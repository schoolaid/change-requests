<?php

use Said\ChangeRequests\ChangeRequestsServiceProvider;

it('registra la conexión change_requests desde la config del paquete', function () {
    $connection = config('database.connections.' . config('change-requests.connection'));

    expect($connection)->not->toBeNull()
        ->and($connection['database'])->toBe(config('change-requests.database.database'));
});

it('no pisa una conexión que la app ya declaró', function () {
    config(['change-requests.connection' => 'cr_probe']);
    config(['database.connections.cr_probe' => ['driver' => 'sqlite', 'database' => ':memory:']]);

    (new ChangeRequestsServiceProvider(app()))->register();

    expect(config('database.connections.cr_probe.driver'))->toBe('sqlite');
});

it('expone el source_app configurado', function () {
    expect(config('change-requests.source_app'))->toBe('marketaid');
});

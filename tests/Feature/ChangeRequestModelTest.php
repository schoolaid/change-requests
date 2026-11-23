<?php

use Illuminate\Support\Facades\Schema;
use Said\ChangeRequests\Enums\ChangeRequestPriority;
use Said\ChangeRequests\Enums\ChangeRequestStatus;
use Said\ChangeRequests\Enums\MessageRole;
use Said\ChangeRequests\Models\ChangeRequest;

beforeEach(function () {
    useChangeRequestsSqlite();
});

it('crea la tabla con la columna source_app', function () {
    $connection = config('change-requests.connection');

    expect(Schema::connection($connection)->hasTable('change_requests'))->toBeTrue()
        ->and(Schema::connection($connection)->hasColumn('change_requests', 'source_app'))->toBeTrue();
});

it('genera uuid al crear y castea los enums', function () {
    $model = ChangeRequest::query()->create([
        'title' => 'Ticket de prueba',
        'description' => 'descripción',
        'created_by_id' => '1',
        'created_by_name' => 'Edwin',
        'created_by_email' => 'edwin@schoolaid.app',
        'source_app' => 'marketaid',
        'messages' => [],
    ]);

    expect($model->uuid)->toBeString()->not->toBeEmpty()
        ->and($model->fresh()->status)->toBe(ChangeRequestStatus::Open)
        ->and($model->fresh()->priority)->toBe(ChangeRequestPriority::Medium)
        ->and($model->fresh()->last_actor)->toBe(MessageRole::Admin)
        ->and($model->fresh()->messages)->toBe([])
        ->and($model->fresh()->images)->toBeNull();
});

it('respeta un uuid provisto', function () {
    $model = ChangeRequest::query()->create([
        'uuid' => '11111111-2222-3333-4444-555555555555',
        'title' => 'x',
        'description' => 'y',
        'created_by_id' => '1',
        'created_by_name' => 'Edwin',
        'created_by_email' => 'edwin@schoolaid.app',
        'source_app' => 'marketaid',
        'messages' => [],
    ]);

    expect($model->uuid)->toBe('11111111-2222-3333-4444-555555555555');
});

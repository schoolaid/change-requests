<?php

use Said\ChangeRequests\Enums\ChangeRequestStatus;

it('permite exactamente las transiciones del contrato', function () {
    $expected = [
        'open' => ['in_progress', 'cancelled'],
        'in_progress' => ['needs_info', 'testing', 'cancelled'],
        'needs_info' => ['in_progress', 'cancelled'],
        'testing' => ['done', 'rejected', 'cancelled'],
        'rejected' => ['in_progress', 'cancelled'],
        'done' => [],
        'cancelled' => [],
    ];

    foreach ($expected as $from => $targets) {
        $actual = array_map(
            fn (ChangeRequestStatus $status) => $status->value,
            ChangeRequestStatus::from($from)->allowedTransitions(),
        );

        expect($actual)->toBe($targets, "transiciones de {$from}");
    }
});

it('reconoce done y cancelled como terminales y rejected como no terminal', function () {
    expect(ChangeRequestStatus::Done->isTerminal())->toBeTrue()
        ->and(ChangeRequestStatus::Cancelled->isTerminal())->toBeTrue()
        ->and(ChangeRequestStatus::Rejected->isTerminal())->toBeFalse();
});

it('valida transiciones puntuales', function () {
    expect(ChangeRequestStatus::Open->canTransitionTo(ChangeRequestStatus::InProgress))->toBeTrue()
        ->and(ChangeRequestStatus::Open->canTransitionTo(ChangeRequestStatus::Done))->toBeFalse()
        ->and(ChangeRequestStatus::Rejected->canTransitionTo(ChangeRequestStatus::InProgress))->toBeTrue();
});

it('resuelve labels en es y en', function () {
    expect(ChangeRequestStatus::Open->label('es'))->toBe('Abierta')
        ->and(ChangeRequestStatus::Open->label('en'))->toBe('Open');
});

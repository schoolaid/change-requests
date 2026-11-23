<?php

use Said\ChangeRequests\DataTransferObjects\Message;
use Said\ChangeRequests\Enums\MessageRole;
use Said\ChangeRequests\Enums\MessageType;

it('serializa con keys camelCase y fecha Y-m-d H:i:s', function () {
    $this->travelTo('2026-07-27 14:30:00');

    $message = Message::make(MessageRole::Admin, MessageType::Answer, "línea 1\nlínea 2", 'Edwin');

    expect($message->toArray())->toBe([
        'role' => 'admin',
        'type' => 'answer',
        'body' => "línea 1\nlínea 2",
        'authorName' => 'Edwin',
        'at' => '2026-07-27 14:30:00',
    ]);
});

it('reconstruye desde un array del hilo', function () {
    $message = Message::fromArray([
        'role' => 'agent',
        'type' => 'result',
        'body' => 'listo',
        'authorName' => 'Orchestrator',
        'at' => '2026-07-19 08:00:00',
    ]);

    expect($message->role)->toBe(MessageRole::Agent)
        ->and($message->type)->toBe(MessageType::Result)
        ->and($message->at)->toBe('2026-07-19 08:00:00');
});

it('tolera entradas sin authorName ni at', function () {
    $message = Message::fromArray(['role' => 'admin', 'type' => 'answer', 'body' => 'x']);

    expect($message->authorName)->toBe('')
        ->and($message->at)->toBe('');
});

it('excluye status_change y result de los tipos elegibles al responder', function () {
    expect(MessageType::replyableValues())
        ->toBe(['request', 'question', 'answer', 'feedback']);
});

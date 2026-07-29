<?php

namespace Said\ChangeRequests\Exceptions;

use RuntimeException;
use Said\ChangeRequests\Enums\ChangeRequestStatus;

class InvalidStatusTransitionException extends RuntimeException
{
    /** @var array<int, string> */
    public readonly array $allowed;

    /**
     * `$from` es null cuando la BD trae un status fuera del enum (lo escribió la
     * app-agente): no hay máquina de estados desde la que razonar, así que no se
     * ofrece ninguna transición.
     */
    public function __construct(
        public readonly ?ChangeRequestStatus $from,
        public readonly ChangeRequestStatus $to,
    ) {
        $this->allowed = $from === null ? [] : array_map(
            fn (ChangeRequestStatus $status) => $status->value,
            $from->allowedTransitions(),
        );

        parent::__construct(
            'Transición inválida: ' . ($from?->value ?? 'desconocido') . " -> {$to->value}.",
        );
    }
}

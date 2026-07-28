<?php

namespace Said\ChangeRequests\Exceptions;

use RuntimeException;
use Said\ChangeRequests\Enums\ChangeRequestStatus;

class InvalidStatusTransitionException extends RuntimeException
{
    /** @var array<int, string> */
    public readonly array $allowed;

    public function __construct(
        public readonly ChangeRequestStatus $from,
        public readonly ChangeRequestStatus $to,
    ) {
        $this->allowed = array_map(
            fn (ChangeRequestStatus $status) => $status->value,
            $from->allowedTransitions(),
        );

        parent::__construct("Transición inválida: {$from->value} -> {$to->value}.");
    }
}

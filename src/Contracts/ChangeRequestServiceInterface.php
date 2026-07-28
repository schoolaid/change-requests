<?php

namespace Said\ChangeRequests\Contracts;

use Said\ChangeRequests\Enums\ChangeRequestStatus;
use Said\ChangeRequests\Enums\MessageType;
use Said\ChangeRequests\Models\ChangeRequest;

interface ChangeRequestServiceInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): ChangeRequest;

    public function reply(string $uuid, string $body, MessageType $type): ChangeRequest;

    public function changeStatus(string $uuid, ChangeRequestStatus $target): ChangeRequest;
}

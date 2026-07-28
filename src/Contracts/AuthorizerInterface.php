<?php

namespace Said\ChangeRequests\Contracts;

use Said\ChangeRequests\Models\ChangeRequest;

interface AuthorizerInterface
{
    public function canViewAny(mixed $user): bool;

    public function canView(mixed $user, ChangeRequest $model): bool;

    public function canCreate(mixed $user): bool;

    public function canReply(mixed $user, ChangeRequest $model): bool;

    public function canChangeStatus(mixed $user, ChangeRequest $model): bool;
}

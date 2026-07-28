<?php

namespace Said\ChangeRequests\Support;

use Said\ChangeRequests\Contracts\AuthorizerInterface;
use Said\ChangeRequests\Models\ChangeRequest;

/**
 * Default portable: el middleware de rutas ya autenticó; el paquete no conoce
 * el sistema de permisos de la app. Las apps con permisos bindean el suyo.
 */
class AllowAuthenticatedAuthorizer implements AuthorizerInterface
{
    public function canViewAny(mixed $user): bool
    {
        return $user !== null;
    }

    public function canView(mixed $user, ChangeRequest $model): bool
    {
        return $user !== null;
    }

    public function canCreate(mixed $user): bool
    {
        return $user !== null;
    }

    public function canReply(mixed $user, ChangeRequest $model): bool
    {
        return $user !== null;
    }

    public function canChangeStatus(mixed $user, ChangeRequest $model): bool
    {
        return $user !== null;
    }
}

<?php

namespace Said\ChangeRequests\Support;

use Said\ChangeRequests\Contracts\ContextResolverInterface;
use Said\ChangeRequests\DataTransferObjects\ChangeRequestContext;

class DefaultContextResolver implements ContextResolverInterface
{
    public function resolve(): ChangeRequestContext
    {
        $user = auth()->user();

        return new ChangeRequestContext(
            sourceApp: (string) config('change-requests.source_app'),
            actorId: (string) ($user?->getAuthIdentifier() ?? ''),
            actorName: (string) ($user?->name ?? $user?->email ?? ''),
            actorEmail: (string) ($user?->email ?? ''),
        );
    }
}

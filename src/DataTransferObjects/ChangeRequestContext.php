<?php

namespace Said\ChangeRequests\DataTransferObjects;

final class ChangeRequestContext
{
    public function __construct(
        public readonly string $sourceApp,
        public readonly string $actorId,
        public readonly string $actorName,
        public readonly string $actorEmail,
        public readonly ?string $organizationId = null,
        public readonly ?string $organizationName = null,
    ) {
    }
}

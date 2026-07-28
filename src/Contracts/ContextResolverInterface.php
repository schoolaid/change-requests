<?php

namespace Said\ChangeRequests\Contracts;

use Said\ChangeRequests\DataTransferObjects\ChangeRequestContext;

interface ContextResolverInterface
{
    public function resolve(): ChangeRequestContext;
}

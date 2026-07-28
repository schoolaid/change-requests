<?php

namespace Said\ChangeRequests\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Said\ChangeRequests\Models\ChangeRequest;

interface ChangeRequestRepositoryInterface
{
    /**
     * @param array{status?: string, priority?: string, lastActor?: string, search?: string, sort?: string} $filters
     */
    public function paginate(array $filters, int $perPage): LengthAwarePaginator;

    public function findByUuid(string $uuid): ?ChangeRequest;

    public function lockByUuid(string $uuid): ?ChangeRequest;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): ChangeRequest;
}

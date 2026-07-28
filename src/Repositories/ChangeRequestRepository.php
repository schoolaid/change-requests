<?php

namespace Said\ChangeRequests\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Said\ChangeRequests\Contracts\ContextResolverInterface;
use Said\ChangeRequests\Models\ChangeRequest;

class ChangeRequestRepository implements ChangeRequestRepositoryInterface
{
    public function __construct(private readonly ContextResolverInterface $context)
    {
    }

    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['lastActor'])) {
            $query->where('last_actor', $filters['lastActor']);
        }

        if (! empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';

            $query->where(function (Builder $inner) use ($search): void {
                $inner->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        $sort = $filters['sort'] ?? '-created_at';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        return $query->orderBy($column, $direction)->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?ChangeRequest
    {
        return $this->baseQuery()->where('uuid', $uuid)->first();
    }

    public function lockByUuid(string $uuid): ?ChangeRequest
    {
        return $this->baseQuery()->where('uuid', $uuid)->lockForUpdate()->first();
    }

    /**
     * Estampa `source_app` desde el contexto, ignorando lo que traiga el array:
     * la frontera de aislamiento la impone el repositorio, no el caller.
     */
    public function create(array $attributes): ChangeRequest
    {
        $attributes['source_app'] = $this->context->resolve()->sourceApp;

        return ChangeRequest::query()->create($attributes);
    }

    /**
     * Toda lectura pasa por acá: el scope por `source_app` no es opcional ni
     * configurable desde el request — la BD es compartida con otras apps.
     * `create()` no la usa; estampa el scope directamente antes de insertar.
     */
    private function baseQuery(): Builder
    {
        return ChangeRequest::query()
            ->where('source_app', $this->context->resolve()->sourceApp);
    }
}

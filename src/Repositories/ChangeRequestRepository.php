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
     * Toda lectura y todo bloqueo pasan por acá: el scope no es opcional ni
     * configurable desde el request. La BD es compartida con otras apps
     * (`source_app`) y, dentro de esta app, con todas las organizaciones
     * (`school_id` = la organización del contexto, el tenant activo).
     *
     * Sin organización en el contexto la consulta no devuelve nada: falla
     * cerrado en vez de abrir los tickets de todas las organizaciones.
     * `create()` no la usa; estampa el scope directamente antes de insertar.
     */
    private function baseQuery(): Builder
    {
        $context = $this->context->resolve();

        $query = ChangeRequest::query()->where('source_app', $context->sourceApp);

        if ($context->organizationId === null || $context->organizationId === '') {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('school_id', $context->organizationId);
    }
}

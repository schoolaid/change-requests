<?php

namespace Said\ChangeRequests\Http\Controllers\Concerns;

use Said\ChangeRequests\Models\ChangeRequest;
use Said\ChangeRequests\Repositories\ChangeRequestRepositoryInterface;

/**
 * El ticket de `{uuid}` dentro del alcance del repositorio (su `source_app`), o 404. Lo comparten
 * los controllers que reciben el uuid en la ruta.
 *
 * @property-read ChangeRequestRepositoryInterface $repository
 */
trait FindsChangeRequestByUuid
{
    private function findChangeRequestOrFail(string $uuid): ChangeRequest
    {
        $model = $this->repository->findByUuid($uuid);

        abort_if($model === null, 404);

        return $model;
    }
}

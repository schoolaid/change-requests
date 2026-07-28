<?php

namespace Said\ChangeRequests\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Said\ChangeRequests\Contracts\AuthorizerInterface;
use Said\ChangeRequests\Contracts\ChangeRequestServiceInterface;
use Said\ChangeRequests\Http\Requests\IndexChangeRequestRequest;
use Said\ChangeRequests\Http\Requests\StoreChangeRequestRequest;
use Said\ChangeRequests\Http\Resources\ChangeRequestListResource;
use Said\ChangeRequests\Http\Resources\ChangeRequestResource;
use Said\ChangeRequests\Models\ChangeRequest;
use Said\ChangeRequests\Repositories\ChangeRequestRepositoryInterface;

class ChangeRequestController extends Controller
{
    public function __construct(
        private readonly ChangeRequestRepositoryInterface $repository,
        private readonly ChangeRequestServiceInterface $service,
        private readonly AuthorizerInterface $authorizer,
    ) {
    }

    public function index(IndexChangeRequestRequest $request): AnonymousResourceCollection
    {
        abort_unless($this->authorizer->canViewAny($request->user()), 403);

        return ChangeRequestListResource::collection(
            $this->repository->paginate($request->filters(), $request->perPage()),
        );
    }

    public function store(StoreChangeRequestRequest $request): JsonResponse
    {
        abort_unless($this->authorizer->canCreate($request->user()), 403);

        $model = $this->service->create($request->validated());

        return (new ChangeRequestResource($model))->response()->setStatusCode(201);
    }

    public function show(Request $request, string $uuid): ChangeRequestResource
    {
        $model = $this->findOrFail($uuid);

        abort_unless($this->authorizer->canView($request->user(), $model), 403);

        return new ChangeRequestResource($model);
    }

    private function findOrFail(string $uuid): ChangeRequest
    {
        $model = $this->repository->findByUuid($uuid);

        abort_if($model === null, 404);

        return $model;
    }
}

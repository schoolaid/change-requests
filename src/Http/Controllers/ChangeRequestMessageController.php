<?php

namespace Said\ChangeRequests\Http\Controllers;

use Illuminate\Routing\Controller;
use Said\ChangeRequests\Contracts\AuthorizerInterface;
use Said\ChangeRequests\Contracts\ChangeRequestServiceInterface;
use Said\ChangeRequests\Http\Requests\ReplyChangeRequestRequest;
use Said\ChangeRequests\Http\Resources\ChangeRequestResource;
use Said\ChangeRequests\Repositories\ChangeRequestRepositoryInterface;

class ChangeRequestMessageController extends Controller
{
    public function __construct(
        private readonly ChangeRequestRepositoryInterface $repository,
        private readonly ChangeRequestServiceInterface $service,
        private readonly AuthorizerInterface $authorizer,
    ) {
    }

    public function store(ReplyChangeRequestRequest $request, string $uuid): ChangeRequestResource
    {
        $model = $this->repository->findByUuid($uuid);

        abort_if($model === null, 404);
        abort_unless($this->authorizer->canReply($request->user(), $model), 403);

        return new ChangeRequestResource(
            $this->service->reply($uuid, $request->string('body')->toString(), $request->messageType()),
        );
    }
}

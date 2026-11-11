<?php

namespace Said\ChangeRequests\Http\Controllers;

use Illuminate\Routing\Controller;
use Said\ChangeRequests\Contracts\AuthorizerInterface;
use Said\ChangeRequests\Contracts\ChangeRequestServiceInterface;
use Said\ChangeRequests\Http\Controllers\Concerns\FindsChangeRequestByUuid;
use Said\ChangeRequests\Http\Requests\ReplyChangeRequestRequest;
use Said\ChangeRequests\Http\Resources\ChangeRequestResource;
use Said\ChangeRequests\Repositories\ChangeRequestRepositoryInterface;

class ChangeRequestMessageController extends Controller
{
    use FindsChangeRequestByUuid;

    public function __construct(
        private readonly ChangeRequestRepositoryInterface $repository,
        private readonly ChangeRequestServiceInterface $service,
        private readonly AuthorizerInterface $authorizer,
    ) {
    }

    public function store(ReplyChangeRequestRequest $request, string $uuid): ChangeRequestResource
    {
        $model = $this->findChangeRequestOrFail($uuid);
        abort_unless($this->authorizer->canReply($request->user(), $model), 403);

        return new ChangeRequestResource(
            $this->service->reply($uuid, $request->string('body')->toString(), $request->messageType()),
        );
    }
}

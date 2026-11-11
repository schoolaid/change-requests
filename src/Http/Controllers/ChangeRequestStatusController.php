<?php

namespace Said\ChangeRequests\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Said\ChangeRequests\Contracts\AuthorizerInterface;
use Said\ChangeRequests\Contracts\ChangeRequestServiceInterface;
use Said\ChangeRequests\Exceptions\InvalidStatusTransitionException;
use Said\ChangeRequests\Http\Controllers\Concerns\FindsChangeRequestByUuid;
use Said\ChangeRequests\Http\Requests\ChangeStatusRequest;
use Said\ChangeRequests\Http\Resources\ChangeRequestResource;
use Said\ChangeRequests\Repositories\ChangeRequestRepositoryInterface;

class ChangeRequestStatusController extends Controller
{
    use FindsChangeRequestByUuid;

    public function __construct(
        private readonly ChangeRequestRepositoryInterface $repository,
        private readonly ChangeRequestServiceInterface $service,
        private readonly AuthorizerInterface $authorizer,
    ) {
    }

    public function update(ChangeStatusRequest $request, string $uuid): ChangeRequestResource|JsonResponse
    {
        $model = $this->findChangeRequestOrFail($uuid);
        abort_unless($this->authorizer->canChangeStatus($request->user(), $model), 403);

        try {
            return new ChangeRequestResource(
                $this->service->changeStatus($uuid, $request->targetStatus()),
            );
        } catch (InvalidStatusTransitionException $exception) {
            return response()->json([
                'message' => __('change-requests::messages.invalidTransition', [
                    'from' => $exception->from?->label()
                        ?? __('change-requests::messages.unknownStatus'),
                    'to' => $exception->to->label(),
                    'allowed' => $exception->allowed === []
                        ? __('change-requests::messages.noTransitionsAvailable')
                        : implode(', ', $exception->allowed),
                ]),
                'allowedTransitions' => $exception->allowed,
            ], 422);
        }
    }
}

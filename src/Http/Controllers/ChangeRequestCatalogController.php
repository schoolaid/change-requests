<?php

namespace Said\ChangeRequests\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Said\ChangeRequests\Contracts\AuthorizerInterface;
use Said\ChangeRequests\Enums\ChangeRequestCategory;
use Said\ChangeRequests\Enums\ChangeRequestPriority;
use Said\ChangeRequests\Enums\ChangeRequestStatus;
use Said\ChangeRequests\Enums\MessageType;
use Said\ChangeRequests\Support\ModuleCatalog;

class ChangeRequestCatalogController extends Controller
{
    public function __construct(
        private readonly ModuleCatalog $modules,
        private readonly AuthorizerInterface $authorizer,
    ) {
    }

    /**
     * Existe para que el cliente no duplique la máquina de estados: cada estado
     * viaja con sus transiciones permitidas.
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless($this->authorizer->canViewAny($request->user()), 403);

        return response()->json([
            'statuses' => array_map(fn (ChangeRequestStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'isTerminal' => $status->isTerminal(),
                'allowedTransitions' => array_map(
                    fn (ChangeRequestStatus $target) => $target->value,
                    $status->allowedTransitions(),
                ),
            ], ChangeRequestStatus::cases()),

            'priorities' => array_map(fn (ChangeRequestPriority $priority) => [
                'value' => $priority->value,
                'label' => $priority->label(),
            ], ChangeRequestPriority::cases()),

            'categories' => array_map(fn (ChangeRequestCategory $category) => [
                'value' => $category->value,
                'label' => $category->label(),
            ], ChangeRequestCategory::cases()),

            'messageTypes' => array_map(fn (string $type) => [
                'value' => $type,
                'label' => MessageType::from($type)->label(),
            ], MessageType::replyableValues()),

            'modules' => array_map(fn (string $slug) => [
                'value' => $slug,
                'label' => $slug,
            ], $this->modules->slugs()),
        ]);
    }
}

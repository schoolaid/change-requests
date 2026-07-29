<?php

namespace Said\ChangeRequests\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Said\ChangeRequests\Contracts\ChangeRequestServiceInterface;
use Said\ChangeRequests\Contracts\ContextResolverInterface;
use Said\ChangeRequests\DataTransferObjects\Message;
use Said\ChangeRequests\Enums\ChangeRequestPriority;
use Said\ChangeRequests\Enums\ChangeRequestStatus;
use Said\ChangeRequests\Enums\MessageRole;
use Said\ChangeRequests\Enums\MessageType;
use Said\ChangeRequests\Exceptions\InvalidStatusTransitionException;
use Said\ChangeRequests\Models\ChangeRequest;
use Said\ChangeRequests\Repositories\ChangeRequestRepositoryInterface;

class ChangeRequestService implements ChangeRequestServiceInterface
{
    /**
     * Claves de `metadata` que captura el cliente. `acceptanceCriteria` es
     * obligatorio (lo valida el FormRequest); el resto es opcional.
     */
    private const METADATA_KEYS = [
        'acceptanceCriteria',
        'reproSteps',
        'affectedArea',
        'affectedModule',
        'constraints',
    ];

    public function __construct(
        private readonly ChangeRequestRepositoryInterface $repository,
        private readonly ContextResolverInterface $context,
    ) {
    }

    public function create(array $data): ChangeRequest
    {
        $context = $this->context->resolve();

        if (trim($context->sourceApp) === '') {
            throw new RuntimeException(
                'change-requests: source_app está vacío. Un ticket sin discriminador de app no es recuperable en la BD compartida.'
            );
        }

        return $this->repository->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? ChangeRequestPriority::Medium->value,
            'category' => $data['category'] ?? null,
            'status' => ChangeRequestStatus::Open->value,
            'metadata' => $this->buildMetadata($data),
            'images' => null,
            'source_app' => $context->sourceApp,
            'created_by_id' => $context->actorId,
            'created_by_name' => $context->actorName,
            'created_by_email' => $context->actorEmail,
            'school_id' => $context->organizationId,
            'school_name' => $context->organizationName,
            'last_actor' => MessageRole::Admin->value,
            'messages' => [],
        ]);
    }

    public function reply(string $uuid, string $body, MessageType $type): ChangeRequest
    {
        $context = $this->context->resolve();

        return $this->transaction(function () use ($uuid, $body, $type, $context): ChangeRequest {
            $model = $this->lockOrFail($uuid);

            $this->appendMessage(
                $model,
                Message::make(MessageRole::Admin, $type, $body, $context->actorName),
            );

            $model->save();

            return $model;
        });
    }

    public function changeStatus(string $uuid, ChangeRequestStatus $target): ChangeRequest
    {
        $context = $this->context->resolve();

        return $this->transaction(function () use ($uuid, $target, $context): ChangeRequest {
            $model = $this->lockOrFail($uuid);
            $current = $model->status;

            if ($current === null || ! $current->canTransitionTo($target)) {
                throw new InvalidStatusTransitionException($current, $target);
            }

            $model->status = $target;

            if ($target->isTerminal()) {
                $model->closed_at = now();
            }

            $this->appendMessage($model, Message::make(
                MessageRole::Admin,
                MessageType::StatusChange,
                // Locale fijo 'es': este body se persiste y lo leen otras apps.
                __('change-requests::messages.statusChanged', [
                    'from' => $current->label('es'),
                    'to' => $target->label('es'),
                ], 'es'),
                $context->actorName,
            ));

            $model->save();

            return $model;
        });
    }

    /**
     * El append sobre la columna JSON es read-modify-write: sin el lock, una
     * escritura concurrente (panel de schoolaid o la app-agente) pierde mensajes.
     */
    private function transaction(callable $callback): ChangeRequest
    {
        return DB::connection(config('change-requests.connection'))->transaction($callback);
    }

    private function lockOrFail(string $uuid): ChangeRequest
    {
        $model = $this->repository->lockByUuid($uuid);

        if ($model === null) {
            throw (new ModelNotFoundException())->setModel(ChangeRequest::class, [$uuid]);
        }

        return $model;
    }

    private function appendMessage(ChangeRequest $model, Message $message): void
    {
        $model->messages = [...($model->messages ?? []), $message->toArray()];
        $model->last_actor = $message->role;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function buildMetadata(array $data): array
    {
        $metadata = [];

        foreach (self::METADATA_KEYS as $key) {
            $value = $data[$key] ?? null;

            if (is_string($value) && trim($value) !== '') {
                $metadata[$key] = trim($value);
            }
        }

        return $metadata;
    }
}

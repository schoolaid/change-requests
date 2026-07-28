<?php

namespace Said\ChangeRequests\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChangeRequestResource extends JsonResource
{
    /**
     * El hilo viaja crudo (keys camelCase del DTO Message): escapar y renderizar
     * los saltos de línea es responsabilidad del cliente.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'priority' => $this->priority->value,
            'status' => $this->status->value,
            'allowedTransitions' => array_map(
                fn ($status) => $status->value,
                $this->status->allowedTransitions(),
            ),
            'lastActor' => $this->last_actor->value,
            'messages' => $this->messages ?? [],
            'metadata' => $this->metadata,
            'images' => $this->images,
            'organizationId' => $this->school_id,
            'organizationName' => $this->school_name,
            'createdById' => $this->created_by_id,
            'createdByName' => $this->created_by_name,
            'createdByEmail' => $this->created_by_email,
            'closedAt' => $this->closed_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}

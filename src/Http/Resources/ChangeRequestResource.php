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
     * Si la app-agente dejó un enum fuera del vocabulario, se devuelve el valor
     * crudo de la BD y `allowedTransitions` queda vacío: desde un estado que
     * marketaid no entiende no se ofrece ningún movimiento.
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
            'priority' => $this->priority?->value ?? $this->getAttributes()['priority'],
            'status' => $this->status?->value ?? $this->getAttributes()['status'],
            'allowedTransitions' => array_map(
                fn ($status) => $status->value,
                $this->status?->allowedTransitions() ?? [],
            ),
            'lastActor' => $this->last_actor?->value ?? $this->getAttributes()['last_actor'],
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

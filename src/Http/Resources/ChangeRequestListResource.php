<?php

namespace Said\ChangeRequests\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChangeRequestListResource extends JsonResource
{
    /**
     * Los tres enums caen al valor crudo de la BD cuando la app-agente escribió
     * algo fuera del vocabulario: se ve el ticket raro en vez de perder la lista.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'category' => $this->category,
            'priority' => $this->priority?->value ?? $this->getAttributes()['priority'],
            'status' => $this->status?->value ?? $this->getAttributes()['status'],
            'lastActor' => $this->last_actor?->value ?? $this->getAttributes()['last_actor'],
            'messageCount' => count($this->messages ?? []),
            'organizationName' => $this->school_name,
            'createdByName' => $this->created_by_name,
            'closedAt' => $this->closed_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}

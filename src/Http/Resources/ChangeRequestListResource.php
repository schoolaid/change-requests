<?php

namespace Said\ChangeRequests\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChangeRequestListResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'category' => $this->category,
            'priority' => $this->priority->value,
            'status' => $this->status->value,
            'lastActor' => $this->last_actor->value,
            'messageCount' => count($this->messages ?? []),
            'organizationName' => $this->school_name,
            'createdByName' => $this->created_by_name,
            'closedAt' => $this->closed_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}

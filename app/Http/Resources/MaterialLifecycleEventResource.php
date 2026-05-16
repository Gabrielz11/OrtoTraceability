<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialLifecycleEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'eventType'  => $this->event_type,
            'materialId' => $this->material_id,
            'surgeryId'  => $this->surgery_id,
            'actorRole'  => $this->actor_role,
            'occurredAt' => $this->occurred_at?->toISOString(),
            'payload'    => $this->payload,
        ];
    }
}

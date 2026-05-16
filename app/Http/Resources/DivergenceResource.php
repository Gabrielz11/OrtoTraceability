<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DivergenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'materialId'      => $this->material_id,
            'surgeryId'       => $this->surgery_id,
            'ruleName'        => $this->rule_name,
            'severity'        => $this->severity,
            'message'         => $this->message,
            'context'         => $this->context,
            'status'          => $this->status,
            'acknowledgedBy'  => $this->acknowledged_by,
            'acknowledgedAt'  => $this->acknowledged_at?->toISOString(),
            'occurredAt'      => $this->occurred_at?->toISOString(),
            'createdAt'       => $this->created_at?->toISOString(),
        ];
    }
}

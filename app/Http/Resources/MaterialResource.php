<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'nome'         => $this->nome,
            'lote'         => $this->lote,
            'numeroSerie'  => $this->numero_serie,
            'validade'     => $this->validade?->toDateString(),
            'fabricante'   => $this->fabricante,
            'status'       => $this->status,
            'observacoes'  => $this->observacoes,
            'isExpired'    => $this->isExpired(),
            'isNearExpiry' => $this->isNearExpiry(),
            'createdAt'    => $this->created_at?->toISOString(),
            'updatedAt'    => $this->updated_at?->toISOString(),
            'lifecycleEvents' => $this->whenLoaded(
                'lifecycleEvents',
                fn () => MaterialLifecycleEventResource::collection($this->lifecycleEvents)
            ),
        ];
    }
}

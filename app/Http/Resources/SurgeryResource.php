<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurgeryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'dataHora'   => $this->data_hora?->toISOString(),
            'hospital'   => $this->hospital,
            'medico'     => $this->medico,
            'paciente'   => $this->paciente,
            'status'     => $this->status,
            'observacoes' => $this->observacoes,
            'createdAt'  => $this->created_at?->toISOString(),
            'updatedAt'  => $this->updated_at?->toISOString(),
            'materials'  => $this->whenLoaded(
                'materials',
                fn () => MaterialResource::collection($this->materials)
            ),
        ];
    }
}

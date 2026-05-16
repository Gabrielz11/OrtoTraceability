<?php

namespace App\Modules\Material\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\Channel;
use App\Modules\Material\Domain\Models\Material;

class MaterialUsed implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public readonly string $eventType;

    public function __construct(
        public readonly int $materialId,
        public readonly int $surgeryId,
        public readonly int $actorId,
        public readonly string $actorRole,
        public readonly string $occurredAt,
        public readonly array $metadata = [],
    ) {
        $this->eventType = 'material.used';
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("surgery.{$this->surgeryId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'material.used';
    }

    public function broadcastWith(): array
    {
        $material = Material::find($this->materialId);
        
        return [
            'type' => $this->eventType,
            'material_id' => $this->materialId,
            'material_name' => $material?->nome ?? 'Material',
            'actor_role' => $this->actorRole,
            'occurred_at' => $this->occurredAt,
        ];
    }
}

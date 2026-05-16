<?php

namespace App\Modules\Surgery\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\Channel;
use App\Modules\Material\Domain\Models\Material;

class MaterialUnlinkedFromSurgery implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public readonly string $eventType;

    public function __construct(
        public readonly int $surgeryId,
        public readonly int $materialId,
        public readonly int $actorId,
        public readonly string $actorRole,
        public readonly string $occurredAt,
        public readonly array $metadata = [],
    ) {
        $this->eventType = 'surgery.material_unlinked';
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("surgery.{$this->surgeryId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'surgery.material_unlinked';
    }

    public function broadcastWith(): array
    {
        $material = Material::find($this->materialId);
        
        return [
            'type' => $this->eventType,
            'material_id' => $this->materialId,
            'material_name' => $material?->nome ?? 'Material',
            'occurred_at' => $this->occurredAt,
        ];
    }
}

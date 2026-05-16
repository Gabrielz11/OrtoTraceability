<?php

namespace App\Modules\Kit\Domain\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KitDespachado implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public readonly string $eventType;

    public function __construct(
        public readonly int $surgeryKitId,
        public readonly int $surgeryId,
        public readonly int $actorId,
        public readonly string $actorRole,
        public readonly string $occurredAt,
        public readonly array $metadata = [],
    ) {
        $this->eventType = 'kit.despachado';
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('operations')];
    }

    public function broadcastWith(): array
    {
        return [
            'surgery_kit_id' => $this->surgeryKitId,
            'surgery_id'     => $this->surgeryId,
            'occurred_at'    => $this->occurredAt,
        ];
    }

    public function broadcastAs(): string
    {
        return 'kit.despachado';
    }
}

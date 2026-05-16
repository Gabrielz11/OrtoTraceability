<?php

namespace App\Modules\Material\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use App\Modules\Material\Domain\Models\Material;

class DivergenceDetected implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public readonly string $eventType;

    public function __construct(
        public readonly int $materialId,
        public readonly ?int $surgeryId,
        public readonly int $actorId,
        public readonly string $actorRole,
        public readonly string $occurredAt,
        public readonly array $divergences = [],
        public readonly array $metadata = [],
    ) {
        $this->eventType = 'material.divergence_detected';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('audit'),
            new PrivateChannel('operations'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'divergence.detected';
    }

    public function broadcastWith(): array
    {
        $hasCritical = collect($this->divergences)
            ->where('severity', 'critical')
            ->isNotEmpty();

        return [
            'material_id'      => $this->materialId,
            'surgery_id'       => $this->surgeryId,
            'divergence_count' => count($this->divergences),
            'has_critical'     => $hasCritical,
            'occurred_at'      => $this->occurredAt,
        ];
    }
}

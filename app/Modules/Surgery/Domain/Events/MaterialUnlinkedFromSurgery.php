<?php

namespace App\Modules\Surgery\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaterialUnlinkedFromSurgery
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
}

<?php

namespace App\Modules\Material\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaterialDeleted
{
    use Dispatchable, SerializesModels;

    public readonly string $eventType;

    public function __construct(
        public readonly int $materialId,
        public readonly int $actorId,
        public readonly string $actorRole,
        public readonly string $occurredAt,
        public readonly ?array $before = null,
        public readonly array $metadata = [],
    ) {
        $this->eventType = 'material.deleted';
    }
}

<?php

namespace App\Modules\Material\Application\Listeners;

use App\Modules\Material\Domain\Models\MaterialLifecycleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Queued listener — records material lifecycle events to the event store.
 * Every material domain event (including surgery-material link events)
 * is persisted for chronological lifecycle reconstruction.
 */
class RecordLifecycleEventListener implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(object $event): void
    {
        // Only record events that involve a material
        if (!property_exists($event, 'materialId')) {
            return;
        }

        MaterialLifecycleEvent::create([
            'event_type'  => $event->eventType,
            'material_id' => $event->materialId,
            'surgery_id'  => property_exists($event, 'surgeryId') ? $event->surgeryId : null,
            'actor_id'    => $event->actorId,
            'actor_role'  => $event->actorRole,
            'occurred_at' => $event->occurredAt,
            'payload'     => $this->buildPayload($event),
        ]);
    }

    private function buildPayload(object $event): array
    {
        $payload = $event->metadata ?? [];

        if (property_exists($event, 'before') && $event->before !== null) {
            $payload['state_before'] = $event->before;
        }

        if (property_exists($event, 'after') && $event->after !== null) {
            $payload['state_after'] = $event->after;
        }

        if (property_exists($event, 'divergences') && !empty($event->divergences)) {
            $payload['divergences'] = $event->divergences;
        }

        return $payload;
    }
}

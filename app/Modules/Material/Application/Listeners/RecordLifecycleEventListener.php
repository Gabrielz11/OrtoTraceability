<?php

namespace App\Modules\Material\Application\Listeners;

use App\Modules\Material\Domain\Models\MaterialLifecycleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Queued listener — records material lifecycle events to the event store.
 * Runs on the 'lifecycle' queue, monitored by Horizon.
 */
class RecordLifecycleEventListener implements ShouldQueue
{
    public string $queue = 'lifecycle';

    private object $event;

    public function handle(object $event): void
    {
        $this->event = $event;

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

    public function tags(): array
    {
        $tags = ['lifecycle'];

        if (property_exists($this->event ?? new \stdClass, 'materialId')) {
            $tags[] = 'material:' . ($this->event->materialId ?? 'unknown');
        }

        return $tags;
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

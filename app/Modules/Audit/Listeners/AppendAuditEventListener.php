<?php

namespace App\Modules\Audit\Listeners;

use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Material\Domain\Events\DivergenceDetected;
use App\Modules\Validation\Domain\Models\Divergence;
use Illuminate\Support\Str;

/**
 * Synchronous listener — writes audit trail immediately.
 * Replaces the old Auditable trait with event-driven audit.
 */
class AppendAuditEventListener
{
    public function handle(object $event): void
    {
        $entityType = Str::before($event->eventType, '.');
        $entityId = $this->resolveEntityId($event, $entityType);

        AuditEvent::create([
            'actor_user_id' => $event->actorId ?? null,
            'action'        => $event->eventType,
            'entity_type'   => $entityType,
            'entity_id'     => $entityId,
            'before'        => property_exists($event, 'before') ? $event->before : null,
            'after'         => property_exists($event, 'after') ? $event->after : null,
            'metadata'      => $this->buildMetadata($event),
        ]);

        if ($event instanceof DivergenceDetected) {
            foreach ($event->divergences as $divergence) {
                Divergence::create([
                    'material_id' => $event->materialId,
                    'surgery_id'  => $event->surgeryId,
                    'rule_name'   => $divergence['rule'] ?? 'unknown',
                    'severity'    => $divergence['severity'],
                    'message'     => $divergence['message'],
                    'context'     => $divergence['context'] ?? null,
                    'occurred_at' => $event->occurredAt,
                ]);
            }
        }
    }

    private function resolveEntityId(object $event, string $entityType): int
    {
        return match ($entityType) {
            'material' => $event->materialId,
            'surgery'  => $event->surgeryId,
            default    => 0,
        };
    }

    private function buildMetadata(object $event): array
    {
        $metadata = $event->metadata ?? [];

        // Include cross-references in metadata for full traceability
        if (property_exists($event, 'surgeryId') && Str::startsWith($event->eventType, 'material.')) {
            $metadata['related_surgery_id'] = $event->surgeryId;
        }

        if (property_exists($event, 'materialId') && Str::startsWith($event->eventType, 'surgery.')) {
            $metadata['related_material_id'] = $event->materialId;
        }

        if (property_exists($event, 'divergences') && !empty($event->divergences)) {
            $metadata['divergences'] = $event->divergences;
        }

        return $metadata;
    }
}

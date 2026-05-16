<?php

namespace App\Modules\Validation\Listeners;

use App\Modules\Material\Domain\Events\DivergenceDetected;
use App\Modules\Material\Domain\Events\MaterialUsed;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Models\Surgery;
use App\Modules\Validation\Engine\ValidationEngine;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Listens to MaterialUsed events and runs the ValidationEngine
 * against the material + surgery pair.
 *
 * If any divergences are found, dispatches a DivergenceDetected event
 * which is then recorded by the existing audit + lifecycle listeners.
 *
 * This listener is SYNCHRONOUS — validation must complete before
 * the response is returned so the user sees immediate feedback.
 */
class TriggerValidationOnMaterialUsed
{
    public function __construct(
        private readonly ValidationEngine $engine,
    ) {}

    public function handle(MaterialUsed $event): void
    {
        $material = Material::find($event->materialId);
        $surgery  = Surgery::find($event->surgeryId);

        if (!$material) {
            return;
        }

        $result = $this->engine->validate($material, $surgery);

        if ($result->failed()) {
            Log::warning('Divergência detectada no uso de material', [
                'material_id'  => $event->materialId,
                'surgery_id'   => $event->surgeryId,
                'divergences'  => $result->toArray(),
                'rules_run'    => $this->engine->registeredRules(),
            ]);

            Event::dispatch(new DivergenceDetected(
                materialId:  $event->materialId,
                surgeryId:   $event->surgeryId,
                actorId:     $event->actorId,
                actorRole:   $event->actorRole,
                occurredAt:  $event->occurredAt,
                divergences: $result->divergences,
                metadata:    array_merge($event->metadata, [
                    'validation_summary' => $result->toArray(),
                ]),
            ));
        }
    }
}

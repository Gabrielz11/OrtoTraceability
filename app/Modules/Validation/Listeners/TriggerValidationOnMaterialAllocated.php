<?php

namespace App\Modules\Validation\Listeners;

use App\Modules\Material\Domain\Events\MaterialAllocatedToSurgery;
use App\Modules\Material\Domain\Events\DivergenceDetected;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Models\Surgery;
use App\Modules\Validation\Engine\ValidationEngine;
use App\Modules\Validation\Rules\MaterialNearExpiryWarningRule;
use App\Modules\Validation\Rules\MaterialNotExpiredRule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Listens to MaterialAllocatedToSurgery events and runs
 * expiry-related validation rules.
 *
 * This catches problems EARLY — when the material is reserved,
 * not when it's already being used in surgery.
 */
class TriggerValidationOnMaterialAllocated
{
    public function handle(MaterialAllocatedToSurgery $event): void
    {
        $material = Material::find($event->materialId);
        $surgery  = Surgery::find($event->surgeryId);

        if (!$material) {
            return;
        }

        // Only run expiry rules at allocation time
        $engine = new ValidationEngine([
            new MaterialNotExpiredRule(),
            new MaterialNearExpiryWarningRule(),
        ]);

        $result = $engine->validate($material, $surgery);

        if ($result->failed()) {
            Log::warning('Divergência detectada na alocação de material', [
                'material_id' => $event->materialId,
                'surgery_id'  => $event->surgeryId,
                'divergences' => $result->toArray(),
            ]);

            Event::dispatch(new DivergenceDetected(
                materialId:  $event->materialId,
                surgeryId:   $event->surgeryId,
                actorId:     $event->actorId,
                actorRole:   $event->actorRole,
                occurredAt:  $event->occurredAt,
                divergences: $result->divergences,
                metadata:    array_merge($event->metadata, [
                    'validation_context' => 'allocation',
                    'validation_summary' => $result->toArray(),
                ]),
            ));
        }
    }
}

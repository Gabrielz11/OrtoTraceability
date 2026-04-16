<?php

namespace App\Modules\Surgery\Application\Services;

use App\Modules\Material\Domain\Events\MaterialAllocatedToSurgery;
use App\Modules\Material\Domain\Events\MaterialUsed;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Events\MaterialLinkedToSurgery;
use App\Modules\Surgery\Domain\Events\MaterialUnlinkedFromSurgery;
use App\Modules\Surgery\Domain\Models\Surgery;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class SurgeryMaterialService
{
    /**
     * Link a material to a surgery (reserve it).
     *
     * Dispatches two events:
     *  - MaterialLinkedToSurgery (surgery perspective → audit on surgery)
     *  - MaterialAllocatedToSurgery (material perspective → lifecycle on material)
     *
     * @throws DomainException
     */
    public function linkMaterial(Surgery $surgery, Material $material): void
    {
        if ($material->status !== 'em_estoque') {
            throw new DomainException('Material não disponível para reserva.');
        }

        if ($material->isExpired()) {
            throw new DomainException('Não é possível vincular material vencido.');
        }

        DB::transaction(function () use ($surgery, $material) {
            $surgery->materials()->attach($material->id, ['acao' => 'reservado']);
            $material->update(['status' => 'reservado']);
        });

        $actorId   = auth()->id();
        $actorRole = auth()->user()->role ?? 'admin';
        $now       = now()->toISOString();
        $meta      = ['ip' => request()->ip()];

        // Surgery-side event (audit on surgery entity)
        Event::dispatch(new MaterialLinkedToSurgery(
            surgeryId:  $surgery->id,
            materialId: $material->id,
            actorId:    $actorId,
            actorRole:  $actorRole,
            occurredAt: $now,
            metadata:   $meta,
        ));

        // Material-side event (lifecycle on material entity)
        Event::dispatch(new MaterialAllocatedToSurgery(
            materialId: $material->id,
            surgeryId:  $surgery->id,
            actorId:    $actorId,
            actorRole:  $actorRole,
            occurredAt: $now,
            metadata:   $meta,
        ));
    }

    /**
     * Unlink a material from a surgery (return to stock).
     *
     * @throws DomainException
     */
    public function unlinkMaterial(Surgery $surgery, Material $material): void
    {
        $pivot = $surgery->materials()
            ->where('material_item_id', $material->id)
            ->first()
            ?->pivot;

        if (!$pivot) {
            throw new DomainException('Material não está vinculado a esta cirurgia.');
        }

        if ($pivot->acao === 'usado') {
            throw new DomainException('Não é possível desvincular um material que já foi usado.');
        }

        DB::transaction(function () use ($surgery, $material) {
            $surgery->materials()->detach($material->id);
            $material->update(['status' => 'em_estoque']);
        });

        $actorId   = auth()->id();
        $actorRole = auth()->user()->role ?? 'admin';
        $now       = now()->toISOString();
        $meta      = ['ip' => request()->ip()];

        Event::dispatch(new MaterialUnlinkedFromSurgery(
            surgeryId:  $surgery->id,
            materialId: $material->id,
            actorId:    $actorId,
            actorRole:  $actorRole,
            occurredAt: $now,
            metadata:   $meta,
        ));
    }

    /**
     * Mark a material as used in a surgery.
     *
     * @throws DomainException
     */
    public function markAsUsed(Surgery $surgery, Material $material): void
    {
        if ($surgery->status === 'cancelada') {
            throw new DomainException('Cirurgia cancelada. Não é possível usar materiais.');
        }

        DB::transaction(function () use ($surgery, $material) {
            $surgery->materials()->updateExistingPivot($material->id, ['acao' => 'usado']);
            $material->update(['status' => 'implantado_usado']);
        });

        Event::dispatch(new MaterialUsed(
            materialId: $material->id,
            surgeryId:  $surgery->id,
            actorId:    auth()->id(),
            actorRole:  auth()->user()->role ?? 'admin',
            occurredAt: now()->toISOString(),
            metadata:   ['ip' => request()->ip()],
        ));
    }
}

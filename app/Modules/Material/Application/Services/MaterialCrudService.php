<?php

namespace App\Modules\Material\Application\Services;

use App\Modules\Material\Domain\Events\MaterialDeleted;
use App\Modules\Material\Domain\Events\MaterialReceived;
use App\Modules\Material\Domain\Events\MaterialUpdated;
use App\Modules\Material\Domain\Models\Material;
use Illuminate\Support\Facades\Event;

class MaterialCrudService
{
    public function store(array $data): Material
    {
        $material = Material::create($data);

        Event::dispatch(new MaterialReceived(
            materialId: $material->id,
            actorId:    auth()->id(),
            actorRole:  auth()->user()->role ?? 'admin',
            occurredAt: now()->toISOString(),
            after:      $material->getAttributes(),
            metadata:   ['ip' => request()->ip()],
        ));

        return $material;
    }

    public function update(Material $material, array $data): Material
    {
        $before = array_intersect_key($material->getOriginal(), $data);

        $material->update($data);

        $after = $material->getChanges();

        if (!empty($after)) {
            Event::dispatch(new MaterialUpdated(
                materialId: $material->id,
                actorId:    auth()->id(),
                actorRole:  auth()->user()->role ?? 'admin',
                occurredAt: now()->toISOString(),
                before:     $before,
                after:      $after,
                metadata:   ['ip' => request()->ip()],
            ));
        }

        return $material;
    }

    public function delete(Material $material): void
    {
        $before = $material->getAttributes();

        $material->delete();

        Event::dispatch(new MaterialDeleted(
            materialId: $material->id,
            actorId:    auth()->id(),
            actorRole:  auth()->user()->role ?? 'admin',
            occurredAt: now()->toISOString(),
            before:     $before,
            metadata:   ['ip' => request()->ip()],
        ));
    }
}

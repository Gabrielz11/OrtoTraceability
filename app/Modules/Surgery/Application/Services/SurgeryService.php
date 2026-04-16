<?php

namespace App\Modules\Surgery\Application\Services;

use App\Modules\Surgery\Domain\Events\SurgeryCancelled;
use App\Modules\Surgery\Domain\Events\SurgeryCompleted;
use App\Modules\Surgery\Domain\Events\SurgeryCreated;
use App\Modules\Surgery\Domain\Events\SurgeryUpdated;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Support\Facades\Event;

class SurgeryService
{
    public function store(array $data): Surgery
    {
        $surgery = Surgery::create($data);

        Event::dispatch(new SurgeryCreated(
            surgeryId:  $surgery->id,
            actorId:    auth()->id(),
            actorRole:  auth()->user()->role ?? 'admin',
            occurredAt: now()->toISOString(),
            after:      $surgery->getAttributes(),
            metadata:   ['ip' => request()->ip()],
        ));

        return $surgery;
    }

    public function update(Surgery $surgery, array $data): Surgery
    {
        $oldStatus = $surgery->status;
        $before = array_intersect_key($surgery->getOriginal(), $data);

        $surgery->update($data);

        $after = $surgery->getChanges();

        if (!empty($after)) {
            Event::dispatch(new SurgeryUpdated(
                surgeryId:  $surgery->id,
                actorId:    auth()->id(),
                actorRole:  auth()->user()->role ?? 'admin',
                occurredAt: now()->toISOString(),
                before:     $before,
                after:      $after,
                metadata:   ['ip' => request()->ip()],
            ));
        }

        // Dispatch specific status-transition events
        if ($oldStatus !== $surgery->status) {
            match ($surgery->status) {
                'realizada' => Event::dispatch(new SurgeryCompleted(
                    surgeryId:  $surgery->id,
                    actorId:    auth()->id(),
                    actorRole:  auth()->user()->role ?? 'admin',
                    occurredAt: now()->toISOString(),
                    metadata:   ['previous_status' => $oldStatus, 'ip' => request()->ip()],
                )),
                'cancelada' => Event::dispatch(new SurgeryCancelled(
                    surgeryId:  $surgery->id,
                    actorId:    auth()->id(),
                    actorRole:  auth()->user()->role ?? 'admin',
                    occurredAt: now()->toISOString(),
                    metadata:   ['previous_status' => $oldStatus, 'ip' => request()->ip()],
                )),
                default => null,
            };
        }

        return $surgery;
    }

    public function delete(Surgery $surgery): void
    {
        $surgery->delete();
    }
}

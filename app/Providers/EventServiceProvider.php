<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// ── Material Events ────────────────────────────────────────────
use App\Modules\Material\Domain\Events\MaterialReceived;
use App\Modules\Material\Domain\Events\MaterialUpdated;
use App\Modules\Material\Domain\Events\MaterialDeleted;
use App\Modules\Material\Domain\Events\MaterialAllocatedToSurgery;
use App\Modules\Material\Domain\Events\MaterialSterilized;
use App\Modules\Material\Domain\Events\MaterialDispatched;
use App\Modules\Material\Domain\Events\MaterialOpened;
use App\Modules\Material\Domain\Events\MaterialUsed;
use App\Modules\Material\Domain\Events\MaterialDiscarded;
use App\Modules\Material\Domain\Events\MaterialReturned;
use App\Modules\Material\Domain\Events\DivergenceDetected;

// ── Surgery Events ─────────────────────────────────────────────
use App\Modules\Surgery\Domain\Events\SurgeryCreated;
use App\Modules\Surgery\Domain\Events\SurgeryUpdated;
use App\Modules\Surgery\Domain\Events\SurgeryCancelled;
use App\Modules\Surgery\Domain\Events\SurgeryCompleted;
use App\Modules\Surgery\Domain\Events\MaterialLinkedToSurgery;
use App\Modules\Surgery\Domain\Events\MaterialUnlinkedFromSurgery;

// ── Stock Events ───────────────────────────────────────────────
use App\Modules\Stock\Domain\Events\StockItemReceived;
use App\Modules\Stock\Domain\Events\StockItemReserved;
use App\Modules\Stock\Domain\Events\StockItemDispatched;
use App\Modules\Stock\Domain\Events\StockItemUsed;
use App\Modules\Stock\Domain\Events\StockItemDiscarded;
use App\Modules\Stock\Domain\Events\StockItemReturned;

// ── Kit Events ─────────────────────────────────────────────────
use App\Modules\Kit\Domain\Events\KitMontado;
use App\Modules\Kit\Domain\Events\KitConferido;
use App\Modules\Kit\Domain\Events\KitDespachado;
use App\Modules\Kit\Domain\Events\KitRecebidoHospital;
use App\Modules\Kit\Domain\Events\KitDevolvido;

// ── Listeners ──────────────────────────────────────────────────
use App\Modules\Audit\Listeners\AppendAuditEventListener;
use App\Modules\Material\Application\Listeners\RecordLifecycleEventListener;

// ── Validation Listeners (Sprint 4) ────────────────────────────
use App\Modules\Validation\Listeners\TriggerValidationOnMaterialUsed;
use App\Modules\Validation\Listeners\TriggerValidationOnMaterialAllocated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Event → Listener mapping.
     *
     * AppendAuditEventListener: sync, writes to audit_logs.
     * RecordLifecycleEventListener: queued, writes to material_lifecycle_events.
     */
    protected $listen = [
        // ── Material CRUD Events ───────────────────────────────
        MaterialReceived::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        MaterialUpdated::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        MaterialDeleted::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],

        // ── Material Lifecycle Events ──────────────────────────
        MaterialAllocatedToSurgery::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
            TriggerValidationOnMaterialAllocated::class,
        ],
        MaterialSterilized::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        MaterialDispatched::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        MaterialOpened::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        MaterialUsed::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
            TriggerValidationOnMaterialUsed::class,
        ],
        MaterialDiscarded::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        MaterialReturned::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        DivergenceDetected::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],

        // ── Surgery CRUD Events ────────────────────────────────
        SurgeryCreated::class => [
            AppendAuditEventListener::class,
        ],
        SurgeryUpdated::class => [
            AppendAuditEventListener::class,
        ],
        SurgeryCancelled::class => [
            AppendAuditEventListener::class,
        ],
        SurgeryCompleted::class => [
            AppendAuditEventListener::class,
        ],

        // ── Surgery-Material Link Events ───────────────────────
        // These also get lifecycle recording because they involve materials
        MaterialLinkedToSurgery::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],
        MaterialUnlinkedFromSurgery::class => [
            AppendAuditEventListener::class,
            RecordLifecycleEventListener::class,
        ],

        // ── Stock Events ───────────────────────────────────────
        StockItemReceived::class => [
            AppendAuditEventListener::class,
        ],
        StockItemReserved::class => [
            AppendAuditEventListener::class,
        ],
        StockItemDispatched::class => [
            AppendAuditEventListener::class,
        ],
        StockItemUsed::class => [
            AppendAuditEventListener::class,
        ],
        StockItemDiscarded::class => [
            AppendAuditEventListener::class,
        ],
        StockItemReturned::class => [
            AppendAuditEventListener::class,
        ],

        // ── Kit Events ─────────────────────────────────────────
        KitMontado::class => [
            AppendAuditEventListener::class,
        ],
        KitConferido::class => [
            AppendAuditEventListener::class,
        ],
        KitDespachado::class => [
            AppendAuditEventListener::class,
        ],
        KitRecebidoHospital::class => [
            AppendAuditEventListener::class,
        ],
        KitDevolvido::class => [
            AppendAuditEventListener::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

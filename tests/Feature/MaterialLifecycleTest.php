<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Material\Application\Services\MaterialCrudService;
use App\Modules\Material\Domain\Events\DivergenceDetected;
use App\Modules\Material\Domain\Events\MaterialReceived;
use App\Modules\Material\Domain\Events\MaterialUpdated;
use App\Modules\Material\Domain\Events\MaterialUsed;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Material\Domain\Models\MaterialLifecycleEvent;
use App\Modules\Surgery\Application\Services\SurgeryMaterialService;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MaterialLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'instrumentator']);
        $this->actingAs($this->user);
    }

    public function test_creating_a_material_dispatches_material_received_event(): void
    {
        Event::fake([MaterialReceived::class]);

        $service = app(MaterialCrudService::class);
        $service->store([
            'nome'      => 'Placa Titânio',
            'lote'      => 'L001',
            'validade'  => now()->addMonths(12)->toDateString(),
            'fabricante' => 'Medtronic',
            'status'    => 'em_estoque',
        ]);

        Event::assertDispatched(MaterialReceived::class);
    }

    public function test_creating_a_material_creates_an_audit_log_entry(): void
    {
        $service = app(MaterialCrudService::class);
        $material = $service->store([
            'nome'       => 'Parafuso Cortical',
            'lote'       => 'L002',
            'validade'   => now()->addMonths(12)->toDateString(),
            'fabricante' => 'Stryker',
            'status'     => 'em_estoque',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'material',
            'entity_id'   => $material->id,
            'action'      => 'material.received',
        ]);
    }

    public function test_creating_a_material_creates_a_lifecycle_event(): void
    {
        $service  = app(MaterialCrudService::class);
        $material = $service->store([
            'nome'       => 'Haste Femoral',
            'lote'       => 'L003',
            'validade'   => now()->addMonths(12)->toDateString(),
            'fabricante' => 'Zimmer',
            'status'     => 'em_estoque',
        ]);

        $this->assertDatabaseHas('material_lifecycle_events', [
            'material_id' => $material->id,
            'event_type'  => 'material.received',
        ]);
    }

    public function test_updating_a_material_dispatches_material_updated_event(): void
    {
        Event::fake([MaterialUpdated::class]);

        $material = Material::factory()->create();
        $service  = app(MaterialCrudService::class);
        $service->update($material, ['nome' => 'Nome Atualizado']);

        Event::assertDispatched(MaterialUpdated::class);
    }

    public function test_marking_a_material_as_used_triggers_validation(): void
    {
        Event::fake([MaterialUsed::class]);

        $material = Material::factory()->create(['status' => 'reservado']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $surgery->materials()->attach($material->id, ['acao' => 'reservado']);

        $service = app(SurgeryMaterialService::class);
        $service->markAsUsed($surgery, $material);

        Event::assertDispatched(MaterialUsed::class, function ($event) use ($material, $surgery) {
            return $event->materialId === $material->id
                && $event->surgeryId === $surgery->id;
        });
    }

    public function test_using_expired_material_dispatches_divergence_detected(): void
    {
        $material = Material::factory()->expired()->create(['status' => 'reservado']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $surgery->materials()->attach($material->id, ['acao' => 'reservado']);

        $service = app(SurgeryMaterialService::class);
        $service->markAsUsed($surgery, $material);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'material',
            'entity_id'   => $material->id,
            'action'      => 'material.divergence_detected',
        ]);
    }

    public function test_using_material_not_linked_to_surgery_dispatches_divergence_detected(): void
    {
        $material = Material::factory()->create(['status' => 'reservado']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        // Intencionalmente NÃO vincula o material à cirurgia

        $service = app(SurgeryMaterialService::class);
        $service->markAsUsed($surgery, $material);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'material',
            'entity_id'   => $material->id,
            'action'      => 'material.divergence_detected',
        ]);
    }
}

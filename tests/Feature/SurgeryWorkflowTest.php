<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Material\Domain\Events\DivergenceDetected;
use App\Modules\Material\Domain\Events\MaterialAllocatedToSurgery;
use App\Modules\Surgery\Application\Services\SurgeryMaterialService;
use App\Modules\Surgery\Application\Services\SurgeryService;
use App\Modules\Surgery\Domain\Events\MaterialLinkedToSurgery;
use App\Modules\Surgery\Domain\Events\SurgeryCancelled;
use App\Modules\Surgery\Domain\Events\SurgeryCompleted;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SurgeryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);
    }

    public function test_completing_a_surgery_dispatches_surgery_completed_event(): void
    {
        Event::fake([SurgeryCompleted::class]);

        $surgery = Surgery::factory()->create(['status' => 'agendada']);
        $service = app(SurgeryService::class);
        $service->update($surgery, array_merge($surgery->only(['data_hora', 'hospital', 'medico', 'paciente']), ['status' => 'realizada']));

        Event::assertDispatched(SurgeryCompleted::class, function ($event) use ($surgery) {
            return $event->surgeryId === $surgery->id;
        });
    }

    public function test_cancelling_a_surgery_dispatches_surgery_cancelled_event(): void
    {
        Event::fake([SurgeryCancelled::class]);

        $surgery = Surgery::factory()->create(['status' => 'agendada']);
        $service = app(SurgeryService::class);
        $service->update($surgery, array_merge($surgery->only(['data_hora', 'hospital', 'medico', 'paciente']), ['status' => 'cancelada']));

        Event::assertDispatched(SurgeryCancelled::class, function ($event) use ($surgery) {
            return $event->surgeryId === $surgery->id;
        });
    }

    public function test_linking_a_material_to_surgery_dispatches_material_allocated_to_surgery(): void
    {
        Event::fake([MaterialAllocatedToSurgery::class, MaterialLinkedToSurgery::class]);

        $material = Material::factory()->create(['status' => 'em_estoque']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $service  = app(SurgeryMaterialService::class);
        $service->linkMaterial($surgery, $material);

        Event::assertDispatched(MaterialAllocatedToSurgery::class, function ($event) use ($material, $surgery) {
            return $event->materialId === $material->id
                && $event->surgeryId === $surgery->id;
        });
    }

    public function test_linking_material_updates_material_status_to_reservado(): void
    {
        $material = Material::factory()->create(['status' => 'em_estoque']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $service  = app(SurgeryMaterialService::class);
        $service->linkMaterial($surgery, $material);

        $this->assertDatabaseHas('material_items', [
            'id'     => $material->id,
            'status' => 'reservado',
        ]);
    }

    public function test_allocation_of_near_expiry_material_dispatches_divergence_detected(): void
    {
        $material = Material::factory()->nearExpiry(10)->create(['status' => 'em_estoque']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $service  = app(SurgeryMaterialService::class);
        $service->linkMaterial($surgery, $material);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'material',
            'entity_id'   => $material->id,
            'action'      => 'material.divergence_detected',
        ]);
    }

    public function test_cannot_link_expired_material_to_surgery(): void
    {
        $material = Material::factory()->expired()->create(['status' => 'em_estoque']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $service  = app(SurgeryMaterialService::class);

        $this->expectException(\DomainException::class);
        $service->linkMaterial($surgery, $material);
    }

    public function test_cannot_link_non_available_material_to_surgery(): void
    {
        $material = Material::factory()->reserved()->create();
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $service  = app(SurgeryMaterialService::class);

        $this->expectException(\DomainException::class);
        $service->linkMaterial($surgery, $material);
    }
}

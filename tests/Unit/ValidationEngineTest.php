<?php

namespace Tests\Unit;

use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Models\Surgery;
use App\Modules\Validation\Engine\ValidationEngine;
use App\Modules\Validation\Engine\ValidationResult;
use App\Modules\Validation\Rules\MaterialAllocatedBeforeUseRule;
use App\Modules\Validation\Rules\MaterialNearExpiryWarningRule;
use App\Modules\Validation\Rules\MaterialNotExpiredRule;
use App\Modules\Validation\Rules\SurgeryNotCancelledRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationEngineTest extends TestCase
{
    use RefreshDatabase;

    // ── ValidationEngine ────────────────────────────────────────

    public function test_empty_engine_returns_passed_result(): void
    {
        $engine   = new ValidationEngine([]);
        $material = Material::factory()->create(['validade' => now()->addMonths(6)]);

        $result = $engine->validate($material);

        $this->assertTrue($result->passed());
        $this->assertCount(0, $result->divergences);
    }

    // ── MaterialNotExpiredRule ───────────────────────────────────

    public function test_not_expired_rule_fails_for_expired_material(): void
    {
        $material = Material::factory()->create(['validade' => now()->subDays(5)]);
        $rule     = new MaterialNotExpiredRule();

        $result = $rule->validate($material);

        $this->assertTrue($result->failed());
        $this->assertCount(1, $result->divergences);
        $this->assertSame('critical', $result->divergences[0]['severity']);
        $this->assertSame('material_not_expired', $result->divergences[0]['rule']);
    }

    public function test_not_expired_rule_passes_for_valid_material(): void
    {
        $material = Material::factory()->create(['validade' => now()->addMonths(6)]);
        $rule     = new MaterialNotExpiredRule();

        $result = $rule->validate($material);

        $this->assertTrue($result->passed());
    }

    // ── MaterialNearExpiryWarningRule ────────────────────────────

    public function test_near_expiry_rule_returns_warning_for_material_expiring_in_15_days(): void
    {
        $material = Material::factory()->create(['validade' => now()->addDays(15)]);
        $rule     = new MaterialNearExpiryWarningRule(30);

        $result = $rule->validate($material);

        $this->assertTrue($result->failed());
        $this->assertSame('warning', $result->divergences[0]['severity']);
        $this->assertSame('material_near_expiry_warning', $result->divergences[0]['rule']);
    }

    public function test_near_expiry_rule_passes_for_material_expiring_in_90_days(): void
    {
        $material = Material::factory()->create(['validade' => now()->addDays(90)]);
        $rule     = new MaterialNearExpiryWarningRule(30);

        $result = $rule->validate($material);

        $this->assertTrue($result->passed());
    }

    // ── MaterialAllocatedBeforeUseRule ───────────────────────────

    public function test_allocated_before_use_rule_fails_when_material_not_linked_to_surgery(): void
    {
        $material = Material::factory()->create(['validade' => now()->addMonths(6)]);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $rule     = new MaterialAllocatedBeforeUseRule();

        $result = $rule->validate($material, $surgery);

        $this->assertTrue($result->failed());
        $this->assertSame('critical', $result->divergences[0]['severity']);
        $this->assertSame('material_allocated_before_use', $result->divergences[0]['rule']);
    }

    public function test_allocated_before_use_rule_passes_when_material_is_linked(): void
    {
        $material = Material::factory()->create(['validade' => now()->addMonths(6), 'status' => 'reservado']);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $surgery->materials()->attach($material->id, ['acao' => 'reservado']);
        $rule = new MaterialAllocatedBeforeUseRule();

        $result = $rule->validate($material, $surgery);

        $this->assertTrue($result->passed());
    }

    public function test_allocated_before_use_rule_passes_when_surgery_is_null(): void
    {
        $material = Material::factory()->create(['validade' => now()->addMonths(6)]);
        $rule     = new MaterialAllocatedBeforeUseRule();

        $result = $rule->validate($material, null);

        $this->assertTrue($result->passed());
    }

    // ── SurgeryNotCancelledRule ──────────────────────────────────

    public function test_surgery_not_cancelled_rule_fails_for_cancelled_surgery(): void
    {
        $material = Material::factory()->create(['validade' => now()->addMonths(6)]);
        $surgery  = Surgery::factory()->create(['status' => 'cancelada']);
        $rule     = new SurgeryNotCancelledRule();

        $result = $rule->validate($material, $surgery);

        $this->assertTrue($result->failed());
        $this->assertSame('critical', $result->divergences[0]['severity']);
        $this->assertSame('surgery_not_cancelled', $result->divergences[0]['rule']);
    }

    public function test_surgery_not_cancelled_rule_passes_for_active_surgery(): void
    {
        $material = Material::factory()->create(['validade' => now()->addMonths(6)]);
        $surgery  = Surgery::factory()->create(['status' => 'agendada']);
        $rule     = new SurgeryNotCancelledRule();

        $result = $rule->validate($material, $surgery);

        $this->assertTrue($result->passed());
    }

    // ── ValidationEngine — merging ───────────────────────────────

    public function test_engine_merges_results_from_multiple_rules(): void
    {
        $material = Material::factory()->create(['validade' => now()->subDays(5)]);
        $surgery  = Surgery::factory()->create(['status' => 'cancelada']);

        $engine = new ValidationEngine([
            new MaterialNotExpiredRule(),
            new SurgeryNotCancelledRule(),
        ]);

        $result = $engine->validate($material, $surgery);

        $this->assertCount(2, $result->divergences);
        $this->assertTrue($result->hasCritical());
    }

    // ── ValidationResult ────────────────────────────────────────

    public function test_validation_result_by_severity_filters_correctly(): void
    {
        $result = new ValidationResult([
            ['rule' => 'r1', 'message' => 'msg', 'severity' => 'critical', 'context' => []],
            ['rule' => 'r2', 'message' => 'msg', 'severity' => 'warning',  'context' => []],
            ['rule' => 'r3', 'message' => 'msg', 'severity' => 'critical', 'context' => []],
        ]);

        $criticals = $result->bySeverity('critical');
        $warnings  = $result->bySeverity('warning');

        $this->assertCount(2, $criticals);
        $this->assertCount(1, $warnings);
    }

    public function test_validation_result_merge_combines_divergences(): void
    {
        $a = new ValidationResult([
            ['rule' => 'r1', 'message' => 'msg', 'severity' => 'critical', 'context' => []],
        ]);
        $b = new ValidationResult([
            ['rule' => 'r2', 'message' => 'msg', 'severity' => 'warning', 'context' => []],
        ]);

        $merged = $a->merge($b);

        $this->assertCount(2, $merged->divergences);
        $this->assertTrue($merged->hasCritical());
        $this->assertTrue($merged->hasWarnings());
    }
}

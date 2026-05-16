<?php

namespace App\Modules\Validation\Rules;

use App\Modules\Validation\Engine\ValidationResult;
use App\Modules\Validation\Engine\ValidationRuleInterface;

/**
 * Rule: Material must be allocated (reserved) to the surgery BEFORE
 * being marked as used in that surgery.
 *
 * Severity: CRITICAL — using a material that was never formally
 * allocated breaks the chain of custody required for traceability.
 */
class MaterialAllocatedBeforeUseRule implements ValidationRuleInterface
{
    public function validate($material, $surgery = null): ValidationResult
    {
        if ($surgery === null) {
            return new ValidationResult();
        }

        // Check if the material is linked to this specific surgery
        $pivot = $surgery->materials()
            ->where('material_item_id', $material->id)
            ->first()
            ?->pivot;

        if (!$pivot) {
            return new ValidationResult([
                [
                    'rule'     => $this->ruleName(),
                    'message'  => "Material '{$material->nome}' não está vinculado à cirurgia #{$surgery->id} antes do uso.",
                    'severity' => 'critical',
                    'context'  => [
                        'material_id' => $material->id,
                        'surgery_id'  => $surgery->id,
                    ],
                ],
            ]);
        }

        return new ValidationResult();
    }

    public function ruleName(): string
    {
        return 'material_allocated_before_use';
    }
}

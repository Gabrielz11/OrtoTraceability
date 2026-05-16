<?php

namespace App\Modules\Validation\Rules;

use App\Modules\Validation\Engine\ValidationResult;
use App\Modules\Validation\Engine\ValidationRuleInterface;

/**
 * Rule: The surgery linked to the material must be in an active
 * status (not cancelled) for the material to be used.
 *
 * Severity: CRITICAL — using materials in a cancelled surgery
 * indicates a process failure.
 */
class SurgeryNotCancelledRule implements ValidationRuleInterface
{
    public function validate($material, $surgery = null): ValidationResult
    {
        if ($surgery === null) {
            return new ValidationResult();
        }

        if ($surgery->status === 'cancelada') {
            return new ValidationResult([
                [
                    'rule'     => $this->ruleName(),
                    'message'  => "Cirurgia #{$surgery->id} está cancelada. Não é permitido usar materiais em cirurgias canceladas.",
                    'severity' => 'critical',
                    'context'  => [
                        'surgery_id'     => $surgery->id,
                        'surgery_status' => $surgery->status,
                        'material_id'    => $material->id,
                    ],
                ],
            ]);
        }

        return new ValidationResult();
    }

    public function ruleName(): string
    {
        return 'surgery_not_cancelled';
    }
}

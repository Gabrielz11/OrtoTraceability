<?php

namespace App\Modules\Validation\Rules;

use App\Modules\Validation\Engine\ValidationResult;
use App\Modules\Validation\Engine\ValidationRuleInterface;

/**
 * Rule: Material must NOT be expired at the time of use.
 *
 * Severity: CRITICAL — using expired material is a serious
 * regulatory violation in medical device traceability.
 */
class MaterialNotExpiredRule implements ValidationRuleInterface
{
    public function validate($material, $surgery = null): ValidationResult
    {
        if ($material->isExpired()) {
            return new ValidationResult([
                [
                    'rule'     => $this->ruleName(),
                    'message'  => "Material '{$material->nome}' (lote {$material->lote}) está vencido desde {$material->validade->format('d/m/Y')}.",
                    'severity' => 'critical',
                    'context'  => [
                        'material_id' => $material->id,
                        'validade'    => $material->validade->toISOString(),
                        'days_past'   => $material->validade->diffInDays(now()),
                    ],
                ],
            ]);
        }

        return new ValidationResult();
    }

    public function ruleName(): string
    {
        return 'material_not_expired';
    }
}

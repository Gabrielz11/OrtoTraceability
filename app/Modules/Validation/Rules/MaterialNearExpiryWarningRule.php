<?php

namespace App\Modules\Validation\Rules;

use App\Modules\Validation\Engine\ValidationResult;
use App\Modules\Validation\Engine\ValidationRuleInterface;

/**
 * Rule: Material that is near expiry (within 30 days) should
 * generate a WARNING divergence — not a block, just a flag
 * for auditors to review.
 *
 * Severity: WARNING — the material CAN still be used, but
 * the auditor should be aware of the risk.
 */
class MaterialNearExpiryWarningRule implements ValidationRuleInterface
{
    public function __construct(
        private readonly int $thresholdDays = 30,
    ) {}

    public function validate($material, $surgery = null): ValidationResult
    {
        if ($material->isNearExpiry($this->thresholdDays)) {
            $daysLeft = $material->validade->diffInDays(now());

            return new ValidationResult([
                [
                    'rule'     => $this->ruleName(),
                    'message'  => "Material '{$material->nome}' (lote {$material->lote}) vence em {$daysLeft} dias ({$material->validade->format('d/m/Y')}).",
                    'severity' => 'warning',
                    'context'  => [
                        'material_id'    => $material->id,
                        'validade'       => $material->validade->toISOString(),
                        'days_remaining' => $daysLeft,
                        'threshold_days' => $this->thresholdDays,
                    ],
                ],
            ]);
        }

        return new ValidationResult();
    }

    public function ruleName(): string
    {
        return 'material_near_expiry_warning';
    }
}

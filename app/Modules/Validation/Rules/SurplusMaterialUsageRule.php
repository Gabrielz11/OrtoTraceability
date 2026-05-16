<?php

namespace App\Modules\Validation\Rules;

use App\Modules\Validation\Engine\ValidationResult;
use App\Modules\Validation\Engine\ValidationRuleInterface;

class SurplusMaterialUsageRule implements ValidationRuleInterface
{
    public function validate($kitItem, $surgery = null): ValidationResult
    {
        // Esta regra opera sobre SurgeryKitItem, não sobre Material/StockItem
        if (!method_exists($kitItem, 'categoria')) {
            return new ValidationResult();
        }

        if ($kitItem->categoria !== 'sobressalente') {
            return new ValidationResult();
        }

        if (!in_array($kitItem->resultado, ['implantado_usado', 'consumido'])) {
            return new ValidationResult();
        }

        if (empty($kitItem->observacao_resultado)) {
            return new ValidationResult([[
                'rule'     => $this->ruleName(),
                'message'  => 'Sobressalente utilizado sem justificativa. Obrigatório registrar motivo '
                            . '(contaminação, queda, quebra, necessidade técnica). RISCO DE GLOSA.',
                'severity' => 'critical',
                'context'  => ['kit_item_id' => $kitItem->id],
            ]]);
        }

        return new ValidationResult([[
            'rule'     => $this->ruleName(),
            'message'  => "Sobressalente utilizado: {$kitItem->observacao_resultado}. "
                        . 'Verificar cobertura na autorização do plano.',
            'severity' => 'warning',
            'context'  => [
                'kit_item_id'   => $kitItem->id,
                'justificativa' => $kitItem->observacao_resultado,
            ],
        ]]);
    }

    public function ruleName(): string
    {
        return 'surplus_material_usage';
    }
}

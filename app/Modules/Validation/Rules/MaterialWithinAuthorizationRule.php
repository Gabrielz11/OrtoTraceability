<?php

namespace App\Modules\Validation\Rules;

use App\Modules\Kit\Domain\Models\SurgeryKitItem;
use App\Modules\Validation\Engine\ValidationResult;
use App\Modules\Validation\Engine\ValidationRuleInterface;

class MaterialWithinAuthorizationRule implements ValidationRuleInterface
{
    public function validate($material, $surgery = null): ValidationResult
    {
        if (!$surgery) {
            return new ValidationResult();
        }

        $authorization = $surgery->authorization;

        if (!$authorization || $authorization->status === 'nao_recebida') {
            return new ValidationResult([[
                'rule'     => $this->ruleName(),
                'message'  => "Cirurgia #{$surgery->id} não possui autorização do plano de saúde cadastrada. "
                            . 'Risco de glosa caso materiais extras sejam utilizados.',
                'severity' => 'warning',
                'context'  => ['surgery_id' => $surgery->id],
            ]]);
        }

        // Para StockItem (novo módulo) verifica código ANVISA
        if (method_exists($material, 'productTemplate')) {
            $codigoProduto = $material->productTemplate->codigo_anvisa
                ?? $material->productTemplate->codigo;

            $itemAutorizado = $authorization->items()
                ->where('codigo_produto', $codigoProduto)
                ->first();

            if (!$itemAutorizado) {
                $nome = $material->productTemplate->nome;
                return new ValidationResult([[
                    'rule'     => $this->ruleName(),
                    'message'  => "Material '{$nome}' não consta na autorização "
                                . "{$authorization->codigo_autorizacao}. RISCO DE GLOSA.",
                    'severity' => 'critical',
                    'context'  => [
                        'stock_item_id'    => $material->id,
                        'authorization_id' => $authorization->id,
                        'codigo_produto'   => $codigoProduto,
                    ],
                ]]);
            }

            $jaUtilizado = SurgeryKitItem::whereHas(
                'surgeryKit',
                fn ($q) => $q->where('surgery_id', $surgery->id)
            )
                ->whereHas(
                    'stockItem.productTemplate',
                    fn ($q) => $q->where('codigo_anvisa', $codigoProduto)
                        ->orWhere('codigo', $codigoProduto)
                )
                ->whereIn('resultado', ['implantado_usado', 'consumido'])
                ->count();

            if ($jaUtilizado >= $itemAutorizado->quantidade_autorizada) {
                return new ValidationResult([[
                    'rule'     => $this->ruleName(),
                    'message'  => "Quantidade autorizada para '{$material->productTemplate->nome}' "
                                . "já atingida ({$itemAutorizado->quantidade_autorizada} unidades). "
                                . 'Uso adicional é GLOSA.',
                    'severity' => 'critical',
                    'context'  => [
                        'autorizado'   => $itemAutorizado->quantidade_autorizada,
                        'ja_utilizado' => $jaUtilizado,
                    ],
                ]]);
            }
        }

        return new ValidationResult();
    }

    public function ruleName(): string
    {
        return 'material_within_authorization';
    }
}

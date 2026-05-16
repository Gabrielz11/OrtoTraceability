<?php

namespace App\Modules\Stock\Application\Services;

use App\Modules\Kit\Domain\Models\KitTemplate;
use App\Modules\Stock\Domain\Models\StockItem;
use Illuminate\Support\Collection;

class StockAlertService
{
    public function kitsComEstoqueInsuficiente(): Collection
    {
        return KitTemplate::where('ativo', true)
            ->with('items.productTemplate')
            ->get()
            ->filter(fn ($kit) => !$kit->podeSerMontado())
            ->map(function ($kit) {
                return [
                    'kit'            => $kit,
                    'itens_faltando' => $kit->itensComEstoqueInsuficiente(),
                ];
            });
    }

    public function itensProximosVencimento(int $dias = 30): Collection
    {
        return StockItem::where('status', 'em_estoque')
            ->whereNotNull('validade')
            ->where('validade', '<=', now()->addDays($dias))
            ->with('productTemplate')
            ->orderBy('validade')
            ->get();
    }

    public function itensVencidos(): Collection
    {
        return StockItem::where('status', 'em_estoque')
            ->whereNotNull('validade')
            ->where('validade', '<', now())
            ->with('productTemplate')
            ->get();
    }
}

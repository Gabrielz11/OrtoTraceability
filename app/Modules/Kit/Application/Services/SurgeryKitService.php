<?php

namespace App\Modules\Kit\Application\Services;

use App\Modules\Kit\Domain\Events\KitConferido;
use App\Modules\Kit\Domain\Events\KitDespachado;
use App\Modules\Kit\Domain\Events\KitDevolvido;
use App\Modules\Kit\Domain\Events\KitMontado;
use App\Modules\Kit\Domain\Events\KitRecebidoHospital;
use App\Modules\Kit\Domain\Models\KitTemplate;
use App\Modules\Kit\Domain\Models\SurgeryKit;
use App\Modules\Kit\Domain\Models\SurgeryKitItem;
use App\Modules\Stock\Domain\Events\StockItemReserved;
use App\Modules\Stock\Domain\Models\StockItem;
use App\Modules\Surgery\Domain\Models\Surgery;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class SurgeryKitService
{
    public function iniciarMontagem(Surgery $surgery, KitTemplate $template): SurgeryKit
    {
        return DB::transaction(function () use ($surgery, $template) {
            $kit = SurgeryKit::create([
                'surgery_id'      => $surgery->id,
                'kit_template_id' => $template->id,
                'created_by'      => auth()->id(),
                'status'          => 'em_separacao',
            ]);

            foreach ($template->items as $templateItem) {
                SurgeryKitItem::create([
                    'surgery_kit_id'       => $kit->id,
                    'kit_template_item_id' => $templateItem->id,
                    'stock_item_id'        => null,
                    'categoria'            => $templateItem->criticidade,
                    'resultado'            => 'pendente',
                ]);
            }

            Event::dispatch(new KitMontado(
                surgeryKitId: $kit->id,
                surgeryId:    $surgery->id,
                actorId:      auth()->id(),
                actorRole:    auth()->user()->role ?? 'admin',
                occurredAt:   now()->toISOString(),
            ));

            return $kit;
        });
    }

    public function vincularItem(SurgeryKitItem $slot, StockItem $stockItem): void
    {
        if ($stockItem->status !== 'em_estoque') {
            throw new DomainException('Item de estoque não disponível (status: ' . $stockItem->status . ').');
        }

        if ($stockItem->isExpired()) {
            throw new DomainException('Item vencido não pode ser vinculado ao kit.');
        }

        DB::transaction(function () use ($slot, $stockItem) {
            $slot->update(['stock_item_id' => $stockItem->id]);
            $stockItem->update(['status' => 'reservado']);
        });

        Event::dispatch(new StockItemReserved(
            stockItemId:  $stockItem->id,
            surgeryKitId: $slot->surgery_kit_id,
            actorId:      auth()->id(),
            actorRole:    auth()->user()->role ?? 'admin',
            occurredAt:   now()->toISOString(),
        ));
    }

    public function desvincularItem(SurgeryKitItem $slot): void
    {
        if (!$slot->stock_item_id) {
            return;
        }

        DB::transaction(function () use ($slot) {
            $slot->stockItem?->update(['status' => 'em_estoque']);
            $slot->update(['stock_item_id' => null]);
        });
    }

    public function conferir(SurgeryKit $kit): void
    {
        $kit->load('essenciais.stockItem');

        if (!$kit->todosEssenciaisConferidos()) {
            $faltando = $kit->essenciais()
                ->whereNull('stock_item_id')
                ->with('kitTemplateItem.productTemplate')
                ->get()
                ->map(fn ($i) => $i->kitTemplateItem->productTemplate->nome)
                ->join(', ');

            throw new DomainException(
                'Conferência bloqueada. Itens essenciais sem material vinculado: ' . $faltando
            );
        }

        $kit->update([
            'status'        => 'conferido',
            'conferido_at'  => now(),
            'conferido_por' => auth()->id(),
        ]);

        Event::dispatch(new KitConferido(
            surgeryKitId: $kit->id,
            surgeryId:    $kit->surgery_id,
            actorId:      auth()->id(),
            actorRole:    auth()->user()->role ?? 'admin',
            occurredAt:   now()->toISOString(),
        ));
    }

    public function despachar(SurgeryKit $kit): void
    {
        if (!$kit->podeSerDespachado()) {
            throw new DomainException('Kit não está conferido para despacho.');
        }

        DB::transaction(function () use ($kit) {
            $kit->update([
                'status'        => 'despachado',
                'despachado_at' => now(),
            ]);

            foreach ($kit->items as $item) {
                $item->stockItem?->update(['status' => 'despachado']);
            }
        });

        Event::dispatch(new KitDespachado(
            surgeryKitId: $kit->id,
            surgeryId:    $kit->surgery_id,
            actorId:      auth()->id(),
            actorRole:    auth()->user()->role ?? 'admin',
            occurredAt:   now()->toISOString(),
        ));
    }

    public function confirmarRecebimento(SurgeryKit $kit): void
    {
        if ($kit->status !== 'despachado') {
            throw new DomainException('Kit não foi despachado ainda.');
        }

        $kit->update([
            'status'      => 'recebido_hospital',
            'recebido_at' => now(),
        ]);

        Event::dispatch(new KitRecebidoHospital(
            surgeryKitId: $kit->id,
            surgeryId:    $kit->surgery_id,
            actorId:      auth()->id(),
            actorRole:    auth()->user()->role ?? 'admin',
            occurredAt:   now()->toISOString(),
        ));
    }

    public function registrarResultado(
        SurgeryKitItem $kitItem,
        string $resultado,
        ?string $motivoDescarte = null,
        ?string $observacao = null
    ): void {
        if ($kitItem->categoria === 'sobressalente'
            && in_array($resultado, ['implantado_usado', 'consumido'])
            && empty($observacao)
        ) {
            throw new DomainException(
                'Sobressalente utilizado exige justificativa (contaminação, queda, necessidade técnica, etc.).'
            );
        }

        DB::transaction(function () use ($kitItem, $resultado, $motivoDescarte, $observacao) {
            $kitItem->update([
                'resultado'            => $resultado,
                'motivo_descarte'      => $motivoDescarte,
                'observacao_resultado' => $observacao,
            ]);

            $newStatus = match ($resultado) {
                'implantado_usado'  => 'implantado_usado',
                'consumido'         => 'consumido',
                'devolvido_intacto' => 'devolvido',
                'descartado'        => 'descartado',
                default             => $kitItem->stockItem?->status,
            };

            if ($newStatus && $kitItem->stockItem) {
                $kitItem->stockItem->update([
                    'status'          => $newStatus,
                    'motivo_descarte' => $motivoDescarte,
                ]);
            }
        });
    }

    public function devolver(SurgeryKit $kit): void
    {
        DB::transaction(function () use ($kit) {
            $kit->update(['status' => 'devolvido']);

            foreach ($kit->items as $item) {
                if ($item->stockItem && $item->resultado === 'pendente') {
                    $item->stockItem->update(['status' => 'em_estoque']);
                    $item->update(['resultado' => 'devolvido_intacto']);
                }
            }
        });

        Event::dispatch(new KitDevolvido(
            surgeryKitId: $kit->id,
            surgeryId:    $kit->surgery_id,
            actorId:      auth()->id(),
            actorRole:    auth()->user()->role ?? 'admin',
            occurredAt:   now()->toISOString(),
        ));
    }
}

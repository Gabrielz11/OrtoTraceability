<?php

namespace App\Modules\Stock\Application\Services;

use App\Modules\Stock\Domain\Events\StockItemDiscarded;
use App\Modules\Stock\Domain\Events\StockItemReceived;
use App\Modules\Stock\Domain\Models\StockItem;
use DomainException;
use Illuminate\Support\Facades\Event;

class StockService
{
    public function store(array $data): StockItem
    {
        $item = StockItem::create($data);

        Event::dispatch(new StockItemReceived(
            stockItemId: $item->id,
            actorId:     auth()->id(),
            actorRole:   auth()->user()->role ?? 'admin',
            occurredAt:  now()->toISOString(),
            metadata:    ['ip' => request()->ip()],
        ));

        return $item;
    }

    public function update(StockItem $item, array $data): StockItem
    {
        $item->update($data);
        return $item;
    }

    public function discard(StockItem $item, string $motivo, ?string $observacao = null): void
    {
        if (in_array($item->status, ['implantado_usado', 'consumido', 'descartado'])) {
            throw new DomainException('Item já não pode ser descartado (status: ' . $item->status . ').');
        }

        $item->update([
            'status'              => 'descartado',
            'motivo_descarte'     => $motivo,
            'observacao_descarte' => $observacao,
        ]);

        Event::dispatch(new StockItemDiscarded(
            stockItemId: $item->id,
            actorId:     auth()->id(),
            actorRole:   auth()->user()->role ?? 'admin',
            occurredAt:  now()->toISOString(),
            metadata:    ['motivo' => $motivo, 'ip' => request()->ip()],
        ));
    }

    public function delete(StockItem $item): void
    {
        $item->delete();
    }
}

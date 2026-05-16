<?php

namespace App\Modules\Authorization\Application\Services;

use App\Modules\Authorization\Domain\Models\Authorization;
use App\Modules\Authorization\Domain\Models\AuthorizationItem;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Support\Facades\DB;

class AuthorizationService
{
    public function store(Surgery $surgery, array $data, array $items = []): Authorization
    {
        return DB::transaction(function () use ($surgery, $data, $items) {
            $authorization = Authorization::create(array_merge($data, [
                'surgery_id'     => $surgery->id,
                'registrado_por' => auth()->id(),
            ]));

            foreach ($items as $item) {
                AuthorizationItem::create(array_merge($item, [
                    'authorization_id' => $authorization->id,
                ]));
            }

            return $authorization;
        });
    }

    public function update(Authorization $authorization, array $data): Authorization
    {
        $authorization->update($data);
        return $authorization;
    }

    public function addItem(Authorization $authorization, array $itemData): AuthorizationItem
    {
        return AuthorizationItem::create(array_merge($itemData, [
            'authorization_id' => $authorization->id,
        ]));
    }

    public function removeItem(AuthorizationItem $item): void
    {
        $item->delete();
    }

    public function marcarRecebida(Authorization $authorization, string $codigo, ?string $observacoes = null): void
    {
        $authorization->update([
            'status'             => 'recebida',
            'codigo_autorizacao' => $codigo,
            'data_autorizacao'   => now(),
            'observacoes'        => $observacoes,
        ]);
    }
}

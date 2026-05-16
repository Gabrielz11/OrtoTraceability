<?php

namespace App\Modules\Kit\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class KitTemplate extends Model
{
    protected $table = 'kit_templates';

    protected $fillable = [
        'nome', 'fabricante', 'procedimento', 'tipo_kit', 'descricao', 'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(KitTemplateItem::class);
    }

    public function essenciais(): HasMany
    {
        return $this->items()->where('criticidade', 'essencial');
    }

    public function sobressalentes(): HasMany
    {
        return $this->items()->where('criticidade', 'sobressalente');
    }

    public function condicionais(): HasMany
    {
        return $this->items()->where('criticidade', 'condicional');
    }

    public function podeSerMontado(): bool
    {
        foreach ($this->essenciais as $item) {
            if ($item->productTemplate->quantidadeDisponivel() < $item->quantidade_minima) {
                return false;
            }
        }
        return true;
    }

    public function itensComEstoqueInsuficiente(): Collection
    {
        return $this->essenciais->filter(function ($item) {
            return $item->productTemplate->quantidadeDisponivel() < $item->quantidade_minima;
        });
    }
}

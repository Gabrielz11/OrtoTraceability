<?php

namespace App\Modules\Stock\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTemplate extends Model
{
    protected $table = 'product_templates';

    protected $fillable = [
        'codigo', 'codigo_anvisa', 'udi_di', 'udi_issuing_agency', 'udi_required',
        'nome', 'fabricante', 'tipo', 'categoria', 'unidade_medida',
        'requer_numero_serie', 'requer_lote', 'ativo', 'observacoes',
    ];

    protected $casts = [
        'udi_required'        => 'boolean',
        'requer_numero_serie' => 'boolean',
        'requer_lote'         => 'boolean',
        'ativo'               => 'boolean',
    ];

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    public function stockItemsInStock(): HasMany
    {
        return $this->hasMany(StockItem::class)
            ->where('status', 'em_estoque')
            ->orderBy('validade');
    }

    public function kitTemplateItems(): HasMany
    {
        return $this->hasMany(\App\Modules\Kit\Domain\Models\KitTemplateItem::class);
    }

    public function quantidadeDisponivel(): int
    {
        return $this->stockItems()->where('status', 'em_estoque')->sum('quantidade');
    }

    public function hasStockAlert(): bool
    {
        return $this->quantidadeDisponivel() === 0;
    }

    public function hasExpiredItems(): bool
    {
        return $this->stockItems()
            ->where('status', 'em_estoque')
            ->whereNotNull('validade')
            ->where('validade', '<', now())
            ->exists();
    }

    public function hasNearExpiryItems(int $days = 30): bool
    {
        return $this->stockItems()
            ->where('status', 'em_estoque')
            ->whereNotNull('validade')
            ->whereBetween('validade', [now(), now()->addDays($days)])
            ->exists();
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'implante_esteril' => 'Implante',
            'instrumental'     => 'Instrumental',
            'consumivel'       => 'Consumível',
            default            => $this->tipo,
        };
    }

    public function categoriaLabel(): string
    {
        return match ($this->categoria) {
            'protese_quadril'    => 'Prótese Quadril',
            'protese_joelho'     => 'Prótese Joelho',
            'protese_ombro'      => 'Prótese Ombro',
            'coluna'             => 'Coluna',
            'trauma'             => 'Trauma',
            'instrumental_geral' => 'Instrumental Geral',
            'consumivel_geral'   => 'Consumível Geral',
            default              => $this->categoria,
        };
    }

    public function isImplante(): bool
    {
        return $this->tipo === 'implante_esteril';
    }

    public function isInstrumental(): bool
    {
        return $this->tipo === 'instrumental';
    }

    public function isConsumivel(): bool
    {
        return $this->tipo === 'consumivel';
    }
}

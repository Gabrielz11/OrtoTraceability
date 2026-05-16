<?php

namespace App\Modules\Stock\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTemplate extends Model
{
    protected $table = 'product_templates';

    protected $fillable = [
        'codigo', 'codigo_anvisa', 'nome', 'fabricante',
        'tipo', 'categoria', 'unidade_medida',
        'requer_numero_serie', 'requer_lote', 'ativo', 'observacoes',
    ];

    protected $casts = [
        'requer_numero_serie' => 'boolean',
        'requer_lote'         => 'boolean',
        'ativo'               => 'boolean',
    ];

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    public function kitTemplateItems(): HasMany
    {
        return $this->hasMany(\App\Modules\Kit\Domain\Models\KitTemplateItem::class);
    }

    public function quantidadeDisponivel(): int
    {
        return $this->stockItems()->where('status', 'em_estoque')->sum('quantidade');
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

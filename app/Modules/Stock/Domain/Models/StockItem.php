<?php

namespace App\Modules\Stock\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_items';

    protected $fillable = [
        'product_template_id', 'lote', 'numero_serie', 'validade',
        'tamanho', 'referencia_fabricante', 'quantidade', 'status',
        'motivo_descarte', 'observacao_descarte',
    ];

    protected $casts = [
        'validade' => 'date',
    ];

    public function productTemplate(): BelongsTo
    {
        return $this->belongsTo(ProductTemplate::class);
    }

    public function isExpired(): bool
    {
        return $this->validade && $this->validade->isPast();
    }

    public function isNearExpiry(int $days = 30): bool
    {
        return $this->validade
            && now()->diffInDays($this->validade) <= $days
            && !$this->isExpired();
    }

    public function isImplante(): bool
    {
        return $this->productTemplate->tipo === 'implante_esteril';
    }

    public function isInstrumental(): bool
    {
        return $this->productTemplate->tipo === 'instrumental';
    }

    public function isConsumivel(): bool
    {
        return $this->productTemplate->tipo === 'consumivel';
    }

    public function isDisponivel(): bool
    {
        return $this->status === 'em_estoque' && !$this->isExpired();
    }
}

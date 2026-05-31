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
        'product_template_id', 'lote', 'numero_serie', 'udi_pi', 'validade',
        'tamanho', 'referencia_fabricante', 'quantidade', 'status',
        'motivo_descarte', 'observacao_descarte',
    ];

    protected $casts = [
        'validade'   => 'date',
        'quantidade' => 'integer',
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

    public function isAvailable(): bool
    {
        return $this->status === 'em_estoque' && !$this->isExpired();
    }

    public function isDisponivel(): bool
    {
        return $this->isAvailable();
    }

    public function expiryBadgeClass(): string
    {
        if ($this->isExpired())    return 'bg-red-100 text-red-700';
        if ($this->isNearExpiry()) return 'bg-yellow-100 text-yellow-700';
        return 'bg-green-100 text-green-700';
    }

    public function expiryLabel(): string
    {
        if ($this->validade === null) return '—';
        if ($this->isExpired())      return 'VENCIDO';
        if ($this->isNearExpiry())   return $this->validade->diffInDays(now()) . 'd restantes';
        return $this->validade->format('m/Y');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'em_estoque'           => 'bg-blue-100 text-blue-700',
            'reservado'            => 'bg-yellow-100 text-yellow-700',
            'despachado'           => 'bg-purple-100 text-purple-700',
            'em_esterilizacao'     => 'bg-orange-100 text-orange-700',
            'pronto_para_cirurgia' => 'bg-teal-100 text-teal-700',
            'implantado_usado'     => 'bg-green-100 text-green-700',
            'consumido'            => 'bg-green-100 text-green-700',
            'descartado'           => 'bg-gray-100 text-gray-500',
            'devolvido'            => 'bg-gray-100 text-gray-500',
            default                => 'bg-gray-100 text-gray-500',
        };
    }
}

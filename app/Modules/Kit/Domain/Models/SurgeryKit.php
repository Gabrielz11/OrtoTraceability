<?php

namespace App\Modules\Kit\Domain\Models;

use App\Models\User;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class SurgeryKit extends Model
{
    protected $table = 'surgery_kits';

    protected $fillable = [
        'surgery_id', 'kit_template_id', 'created_by', 'status',
        'conferido_at', 'conferido_por', 'despachado_at', 'recebido_at', 'observacoes',
    ];

    protected $casts = [
        'conferido_at'  => 'datetime',
        'despachado_at' => 'datetime',
        'recebido_at'   => 'datetime',
    ];

    public function surgery(): BelongsTo
    {
        return $this->belongsTo(Surgery::class);
    }

    public function kitTemplate(): BelongsTo
    {
        return $this->belongsTo(KitTemplate::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function conferidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conferido_por');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SurgeryKitItem::class);
    }

    public function essenciais(): HasMany
    {
        return $this->items()->where('categoria', 'essencial');
    }

    public function sobressalentes(): HasMany
    {
        return $this->items()->where('categoria', 'sobressalente');
    }

    public function todosEssenciaisConferidos(): bool
    {
        return $this->essenciais->every(function ($item) {
            return $item->stock_item_id !== null
                && !$item->stockItem->isExpired();
        });
    }

    public function podeSerDespachado(): bool
    {
        return $this->status === 'conferido' && $this->todosEssenciaisConferidos();
    }

    public function itensSobressalentesUsados(): Collection
    {
        return $this->sobressalentes()
            ->whereIn('resultado', ['implantado_usado', 'consumido'])
            ->get();
    }

    public function hasSobressalenteSemJustificativa(): bool
    {
        return $this->itensSobressalentesUsados()
            ->filter(fn ($i) => empty($i->observacao_resultado))
            ->isNotEmpty();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'em_separacao'           => 'Em Separação',
            'aguardando_conferencia' => 'Aguardando Conferência',
            'conferido'              => 'Conferido',
            'despachado'             => 'Despachado',
            'recebido_hospital'      => 'Recebido no Hospital',
            'em_esterilizacao'       => 'Em Esterilização',
            'pronto'                 => 'Pronto',
            'utilizado'              => 'Utilizado',
            'devolvido'              => 'Devolvido',
            default                  => ucfirst($this->status),
        };
    }
}

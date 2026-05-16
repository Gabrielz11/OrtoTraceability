<?php

namespace App\Modules\Authorization\Domain\Models;

use App\Models\User;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Authorization extends Model
{
    protected $table = 'authorizations';

    protected $fillable = [
        'surgery_id', 'plano_saude', 'codigo_autorizacao',
        'data_autorizacao', 'validade_autorizacao',
        'status', 'observacoes', 'registrado_por',
    ];

    protected $casts = [
        'data_autorizacao'     => 'date',
        'validade_autorizacao' => 'date',
    ];

    public function surgery(): BelongsTo
    {
        return $this->belongsTo(Surgery::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AuthorizationItem::class);
    }

    public function isVencida(): bool
    {
        return $this->validade_autorizacao && $this->validade_autorizacao->isPast();
    }

    public function isRecebida(): bool
    {
        return in_array($this->status, ['recebida', 'parcial']);
    }
}

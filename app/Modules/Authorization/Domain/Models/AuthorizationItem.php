<?php

namespace App\Modules\Authorization\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationItem extends Model
{
    protected $table = 'authorization_items';

    protected $fillable = [
        'authorization_id', 'codigo_produto', 'descricao_produto',
        'quantidade_autorizada', 'valor_unitario', 'coberto',
    ];

    protected $casts = [
        'coberto'             => 'boolean',
        'valor_unitario'      => 'decimal:2',
        'quantidade_autorizada' => 'integer',
    ];

    public function authorization(): BelongsTo
    {
        return $this->belongsTo(Authorization::class);
    }
}

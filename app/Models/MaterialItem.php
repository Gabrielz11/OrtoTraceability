<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialItem extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'lote',
        'numero_serie',
        'validade',
        'fabricante',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'validade' => 'date',
    ];

    public function surgeries()
    {
        return $this->belongsToMany(Surgery::class , 'surgery_material')
            ->withPivot('acao')
            ->withTimestamps();
    }

    public function isExpired()
    {
        return $this->validade->isPast();
    }

    public function isNearExpiry($days = 30)
    {
        return $this->validade->diffInDays(now()) <= $days && !$this->isExpired();
    }
}

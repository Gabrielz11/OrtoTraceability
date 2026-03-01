<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surgery extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'data_hora',
        'hospital',
        'medico',
        'paciente',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function materials()
    {
        return $this->belongsToMany(MaterialItem::class , 'surgery_material')
            ->withPivot('acao')
            ->withTimestamps();
    }
}

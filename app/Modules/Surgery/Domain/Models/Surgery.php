<?php

namespace App\Modules\Surgery\Domain\Models;

use App\Modules\Material\Domain\Models\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surgery extends Model
{
    use SoftDeletes;

    protected $table = 'surgeries';

    protected $fillable = [
        'data_hora',
        'hospital',
        'medico',
        'paciente',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'surgery_material', 'surgery_id', 'material_item_id')
            ->withPivot('acao')
            ->withTimestamps();
    }
}

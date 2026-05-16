<?php

namespace App\Modules\Surgery\Domain\Models;

use App\Modules\Material\Domain\Models\Material;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surgery extends Model
{
    use HasFactory, SoftDeletes;

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

    protected static function newFactory()
    {
        return \Database\Factories\SurgeryFactory::new();
    }

    // ── Relationships ──────────────────────────────────────────

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'surgery_material', 'surgery_id', 'material_item_id')
            ->withPivot('acao')
            ->withTimestamps();
    }
}

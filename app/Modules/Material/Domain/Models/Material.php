<?php

namespace App\Modules\Material\Domain\Models;

use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'material_items';

    protected $fillable = [
        'nome',
        'lote',
        'numero_serie',
        'validade',
        'fabricante',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'validade' => 'date',
    ];

    protected static function newFactory()
    {
        return \Database\Factories\MaterialFactory::new();
    }

    // ── Relationships ──────────────────────────────────────────

    public function surgeries()
    {
        return $this->belongsToMany(Surgery::class, 'surgery_material', 'material_item_id', 'surgery_id')
            ->withPivot('acao')
            ->withTimestamps();
    }

    public function lifecycleEvents()
    {
        return $this->hasMany(MaterialLifecycleEvent::class, 'material_id')
            ->orderBy('occurred_at');
    }

    // ── Domain Methods ─────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->validade->isPast();
    }

    public function isNearExpiry(int $days = 30): bool
    {
        // now()->diffInDays(validade) returns positive for future dates (Carbon 3 compatible)
        return now()->diffInDays($this->validade) <= $days && !$this->isExpired();
    }
}

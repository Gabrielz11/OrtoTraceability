<?php

namespace App\Modules\Material\Domain\Models;

use App\Models\User;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Database\Eloquent\Model;

class MaterialLifecycleEvent extends Model
{
    protected $table = 'material_lifecycle_events';

    protected $fillable = [
        'event_type',
        'material_id',
        'surgery_id',
        'actor_id',
        'actor_role',
        'occurred_at',
        'payload',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'payload' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function surgery()
    {
        return $this->belongsTo(Surgery::class, 'surgery_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}

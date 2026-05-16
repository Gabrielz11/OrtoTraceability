<?php

namespace App\Modules\Validation\Domain\Models;

use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Models\Surgery;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Divergence extends Model
{
    protected $fillable = [
        'material_id',
        'surgery_id',
        'rule_name',
        'severity',
        'message',
        'context',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'occurred_at',
    ];

    protected $casts = [
        'context'         => 'array',
        'acknowledged_at' => 'datetime',
        'occurred_at'     => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function surgery(): BelongsTo
    {
        return $this->belongsTo(Surgery::class, 'surgery_id');
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}

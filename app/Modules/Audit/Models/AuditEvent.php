<?php

namespace App\Modules\Audit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditEvent extends Model
{
    /**
     * Reuses the existing audit_logs table.
     * No new migration needed — schema is compatible.
     */
    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'actor_user_id',
        'action',
        'entity_type',
        'entity_id',
        'before',
        'after',
        'metadata',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function entity()
    {
        return $this->morphTo();
    }
}

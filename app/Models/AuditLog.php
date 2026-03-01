<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false; // We use created_at only from migration

    protected $fillable = [
        'actor_user_id',
        'action',
        'entity_type',
        'entity_id',
        'before',
        'after',
        'metadata'
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'actor_user_id');
    }

    public function entity()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAudit('create', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $before = array_intersect_key($model->getOriginal(), $model->getDirty());
            $after = $model->getDirty();

            if (!empty($after)) {
                $model->logAudit('update', $before, $after);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('delete', $model->getAttributes(), null);
        });
    }

    public function logAudit($action, $before = null, $after = null, $metadata = null)
    {
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => get_class($this),
            'entity_id' => $this->id,
            'before' => $before,
            'after' => $after,
            'metadata' => $metadata
        ]);
    }
}

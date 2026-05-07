<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditables
{
    public static function bootAuditables()
    {
        static::created(function ($model) {
            $model->logAudit('created');
        });

        static::updated(function ($model) {
            $model->logAudit('updated');
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted');
        });
    }

    protected function logAudit(string $event)
    {
        $oldValues = $event === 'updated' ? array_intersect_key($this->getOriginal(), $this->getDirty()) : null;
        $newValues = $event === 'updated' ? $this->getDirty() : ($event === 'created' ? $this->getAttributes() : null);

        // Remover campos sensíveis ou irrelevantes
        $ignore = ['password', 'remember_token', 'updated_at', 'created_at', 'deleted_at', 'email_verified_at'];
        if ($oldValues) $oldValues = array_diff_key($oldValues, array_flip($ignore));
        if ($newValues) $newValues = array_diff_key($newValues, array_flip($ignore));

        // Se não houve mudança real em campos relevantes no update, não loga
        if ($event === 'updated' && empty($newValues)) {
            return;
        }

        AuditLog::create([
            'user_id'        => Auth::id(),
            'event'          => $event,
            'auditable_type' => get_class($this),
            'auditable_id'   => $this->id,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'url'            => Request::fullUrl(),
            'ip_address'     => request()->header('X-Forwarded-For') ?? request()->ip(),
            'user_agent'     => Request::header('User-Agent'),
        ]);
    }
}

<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('created', null, $model->toArray());
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();

            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldValues = array_intersect_key($original, $changes);
                $model->logAudit('updated', $oldValues, $changes);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->toArray(), null);
        });
    }

    protected function logAudit(string $actionType, ?array $oldValues, ?array $newValues): void
    {
        try {
            AuditLog::create([
                'user_id'     => Auth::id(),
                'module_name' => $this->getAuditModule(),
                'action_type' => $actionType,
                'entity_name' => $this->getTable(),
                'entity_id'   => $this->getKey(),
                'old_values'  => $oldValues,
                'new_values'  => $newValues,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function getAuditModule(): string
    {
        return property_exists($this, 'auditModule')
            ? $this->auditModule
            : class_basename($this);
    }
}

<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(
        string $event,
        string $description,
        ?Model $model = null,
        array $oldValues = [],
        array $newValues = []
    ): ?AuditLog {
        try {
            $user = Auth::user();

            return AuditLog::create([
                'user_id'        => $user?->id,
                'event'          => $event,
                'auditable_type' => $model ? get_class($model) : null,
                'auditable_id'   => $model?->getKey(),
                'old_values'     => $oldValues,
                'new_values'     => $newValues,
                'ip_address'     => Request::ip() ?? '127.0.0.1',
                'user_agent'     => substr(Request::userAgent() ?? 'System', 0, 255),
                'description'    => $description,
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently catch audit log issues to avoid breaking main workflow
            report($e);
            return null;
        }
    }
}

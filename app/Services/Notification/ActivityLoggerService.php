<?php

namespace App\Services\Notification;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLoggerService
{
    /**
     * Enregistre une action sur une entité (section 26).
     */
    public function log(string $action, Model $entity, ?array $oldValues = null, ?array $newValues = null): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}

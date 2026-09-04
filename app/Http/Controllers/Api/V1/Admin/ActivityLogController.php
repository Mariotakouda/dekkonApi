<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ActivityLogResource;
use App\Models\ActivityLog;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $logs = ActivityLog::with('user')
            ->when($request->query('entity_type'), fn ($q, $t) => $q->where('entity_type', $t))
            ->when($request->query('action'), fn ($q, $a) => $q->where('action', $a))
            ->when($request->query('user_id'), fn ($q, $u) => $q->where('user_id', $u))
            ->latest()
            ->paginate($request->integer('per_page', 30));

        return $this->paginated($logs, ActivityLogResource::class);
    }
}

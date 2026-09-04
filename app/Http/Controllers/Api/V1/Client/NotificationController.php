<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\NotificationResource;
use App\Models\Notification;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($notifications, NotificationResource::class);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== $request->user()->id, 403, 'Accès non autorisé.');

        $notification->markAsRead();

        return $this->success(new NotificationResource($notification), 'Notification marquée comme lue.');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->unread()->update(['read_at' => now()]);

        return $this->success(message: 'Toutes les notifications ont été marquées comme lues.');
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->notifications()->unread()->count();

        return $this->success(['unread_count' => $count]);
    }
}

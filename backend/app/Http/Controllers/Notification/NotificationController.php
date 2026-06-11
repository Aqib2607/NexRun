<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\ApiController;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->recent()
            ->paginate($request->get('per_page', 20));

        $data = $notifications->through(fn($n) => [
            'id'                => $n->id,
            'notification_type' => $n->notification_type,
            'title'             => $n->title,
            'message'           => $n->message,
            'channel'           => $n->channel,
            'read'              => $n->read_status,
            'data'              => $n->data,
            'sent_at'           => $n->sent_at?->toIso8601String(),
        ]);

        return $this->paginated($data);
    }

    public function markAsRead(int $id, Request $request): JsonResponse
    {
        $notification = Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->markAsRead();
        return $this->success(null, 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->unread()
            ->update(['read_status' => true, 'read_at' => now()]);

        return $this->success(null, 'All notifications marked as read.');
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('user_id', $request->user()->id)->unread()->count();
        return $this->success(['count' => $count]);
    }
}

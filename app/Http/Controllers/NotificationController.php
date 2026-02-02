<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        // $this->middleware('auth');
    }

    /**
     * Display recent notifications (read & unread)
     */
    public function recent(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'notifications' => $this->notificationService
                ->getRecent($user->id)
                ->map(fn ($n) => [
                    'id'         => $n->id,
                    'type'       => $n->type,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'url'        => $n->url,
                    'read_at'    => $n->read_at,
                    'created_at'=> $n->created_at,
                ]),
            'unread_count' => $user->unreadNotificationsCount,
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $this->notificationService->markAsRead($notification->id);

        return response()->json(['status' => 'ok']);
    }

    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService
            ->markAllAsRead(auth()->id());

        return response()->json([
            'status' => 'ok',
            'count'  => $count,
        ]);
    }
}

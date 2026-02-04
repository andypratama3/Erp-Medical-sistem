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

    public function index()
    {
        $user_id = auth()->user()->id;
        $notifications = Notification::where('user_id', $user_id)->paginate(15);

        $columns = [
                ['key' => 'type', 'label' => 'Type', 'type' => 'text'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                ['key' => 'message', 'label' => 'Message', 'type' => 'text'],
                ['key' => 'url', 'label' => 'URL', 'type' => 'text'],
                ['key' => 'data', 'label' => 'Data', 'type' => 'text'],
                ['key' => 'read_at', 'label' => 'Read At', 'type' => 'text'],
            ];

        $notificationsData = $notifications->getCollection()->map(function ($product) {
            return [
                'type' => [
                    'value' => ucfirst($product->status),
                    'label' => match($product->status) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'discontinued' => 'Discontinued',
                        default => 'Gray',
                    }
                ],
                'title' => [
                    'value' => $product->name,
                    'label' => 'Title',
                    'type' => 'text',
                ],
                'message' => [
                    'value' => $product->message,
                    'label' => 'Message',
                    'type' => 'text',
                ],
                'url' => [
                    'value' => $product->url,
                    'label' => 'URL',
                    'type' => 'text',
                ],
                'data' => [
                    'value' => $product->data,
                    'label' => 'Data',
                    'type' => 'text',
                ],
                'read_at' => [
                    'value' => $product->read_at,
                    'label' => 'Read At',
                    'type' => 'text',
                ],
            ];
        })->toArray();

        return view('pages.notification.index', compact('notifications','columns','notificationsData'));
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

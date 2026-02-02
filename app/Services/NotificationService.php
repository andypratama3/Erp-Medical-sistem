<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Centralized Notification Service
 * 
 * Handles all notification operations:
 * - Sending notifications
 * - Marking as read
 * - Retrieving notifications
 * - Notification preferences
 */
class NotificationService
{
    /**
     * Send a notification to a user
     * 
     * @param array $data Notification data
     * @return Notification|null
     */
    public function send(array $data): ?Notification
    {
        try {
            // Validate required fields
            if (!isset($data['user_id']) || !isset($data['type']) || !isset($data['title'])) {
                throw new \InvalidArgumentException('Missing required notification fields');
            }

            // Create notification
            $notification = Notification::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'] ?? null,
                'url' => $data['url'] ?? null,
                'data' => isset($data['data']) ? json_encode($data['data']) : null,
                'read_at' => null,
            ]);

            Log::info("Notification sent", [
                'notification_id' => $notification->id,
                'user_id' => $data['user_id'],
                'type' => $data['type'],
            ]);

            return $notification;

        } catch (\Exception $e) {
            Log::error("Failed to send notification", [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send notification to multiple users
     * 
     * @param array $userIds Array of user IDs
     * @param array $notificationData Notification data (without user_id)
     * @return int Number of notifications sent
     */
    public function sendToMany(array $userIds, array $notificationData): int
    {
        $count = 0;
        
        foreach ($userIds as $userId) {
            $data = array_merge(['user_id' => $userId], $notificationData);
            if ($this->send($data)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Send notification to all users with a specific role
     * 
     * @param string $roleName Role name (e.g., 'wqs', 'scm')
     * @param array $notificationData Notification data
     * @param int|null $branchId Optional branch filter
     * @return int Number of notifications sent
     */
    public function sendToRole(string $roleName, array $notificationData, ?int $branchId = null): int
    {
        $query = User::role($roleName);
        
        if ($branchId) {
            $query->where('current_branch_id', $branchId);
        }
        
        $users = $query->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendToMany($userIds, $notificationData);
    }

    /**
     * Mark notification as read
     * 
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId): bool
    {
        try {
            $notification = Notification::find($notificationId);
            
            if (!$notification) {
                return false;
            }

            if (!$notification->read_at) {
                $notification->update(['read_at' => now()]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to mark notification as read", [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead(int $userId): int
    {
        try {
            return Notification::where('user_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

        } catch (\Exception $e) {
            Log::error("Failed to mark all notifications as read", [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get unread notifications for a user
     * 
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnread(int $userId, int $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notification count for a user
     * 
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get recent notifications (read and unread)
     * 
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecent(int $userId, int $limit = 20)
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Delete old read notifications
     * 
     * @param int $daysOld Delete notifications older than this many days
     * @return int Number of deleted notifications
     */
    public function deleteOldNotifications(int $daysOld = 30): int
    {
        return Notification::whereNotNull('read_at')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}

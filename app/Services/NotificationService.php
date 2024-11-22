<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NotificationEvent;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send($userId, $message, $roomName, $roomId)
    {
        try {

            Log::info("test sebelum notif create on service");
            Log::info([$userId, $message, $roomName, $roomId]);
            $notification = Notification::create([
                'user_id' => $userId,
                'message' => $message,
                'room_name' => $roomName,
                'room_id' => $roomId,
                'read' => false,
            ]);

            Log::info("test");
            event(new NotificationEvent($userId, $message, $roomName, $roomId));
            Log::info("testLagi");

            return $notification;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function markAsRead($notificationId)
    {
        return Notification::where('id', $notificationId)
            ->update(['read' => true]);
    }

    public function getUserNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

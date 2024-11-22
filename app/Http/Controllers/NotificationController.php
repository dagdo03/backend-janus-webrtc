<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController extends BaseController
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Later used on UserController inviteUser function (idk why i code like this)
     */
    public function send($userId, $message, $roomName, $roomId)
    {
        try {
            $notification = $this->notificationService->send($userId, $message, $roomName, $roomId);
            return $notification;
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function index()
    {
        try {
            $userId = auth('sanctum')->id();
            $notifications = $this->notificationService->getUserNotifications($userId);
            return $this->sendResponse("Successfully get user's notifications", $notifications);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function markAsRead($id)
    {
        try {
            $this->notificationService->markAsRead($id);
            return $this->sendResponse(['message' => 'Notification marked as read']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}

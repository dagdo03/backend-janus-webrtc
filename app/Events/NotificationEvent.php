<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Event Fields
     */
    public $message;
    public $userId;
    public $roomName;
    public $roomId;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $message, $roomName, $roomId)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->roomName = $roomName;
        $this->roomId = $roomId;
    }

    /**
     * Get the channels the event should broadcast on. 
     * Each user has their own channel to listen to (see line 41).
     *
     */
    public function broadcastOn()
    {
        return new Channel('notifications.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'notifications.created';
    }

    /**
     * Public function to handle broadcast message.
     */
    public function broadcastMessage()
    {
        return [
            'roomName' => $this->roomName,
            'message' => $this->message,
            'roomId' => $this->roomId
        ];
    }
}

<?php

use App\Events\NotificationEvent;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function test_notification_event_dispatched()
    {
        Event::fake();

        $message = 'Test notification';
        $userId = 1;

        event(new NotificationEvent($message, $userId));

        Event::assertDispatched(NotificationEvent::class, function ($event) use ($message, $userId) {
            return $event->message === $message && $event->userId === $userId;
        });
    }
}

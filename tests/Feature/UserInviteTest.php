<?php

namespace Tests\Feature;

use App\Events\NotificationEvent;
use App\Models\JanusRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserInviteTest extends TestCase
{
    use RefreshDatabase;

    public function testInviteUserSuccessfully()
    {
        // Arrange: Create users for the test (inviting user and the invited user)
        $invitingUser = User::factory()->create();
        $invitedUser = User::factory()->create();
        $roomName = 'Test Room';
        $password = 'securepassword';

        // Act: Mock the NotificationEvent to verify it is dispatched
        Event::fake();

        // Set up the request payload
        $payload = [
            'user_id' => $invitedUser->id,
            'room_name' => $roomName,
            'password' => $password,
        ];

        // Send the request with the inviting user authenticated
        $response = $this->actingAs($invitingUser)
            ->postJson('/api/user/invite', $payload);

        // Assert: Check that the response is successful and the message is correct
        $response->assertStatus(200)
            ->assertJson(['message' => 'Notification Sent!']);

        // Assert: Verify that the event was dispatched with correct data
        Event::assertDispatched(NotificationEvent::class, function ($event) use ($invitedUser, $roomName) {
            return $event->message === 'You have been invited to ' . $roomName &&
                $event->userId === $invitedUser->id &&
                $event->roomName === $roomName;
        });

        // Assert: Check that the room was created in the database
        $this->assertDatabaseHas('janus_room', [
            'owner_id' => $invitingUser->id,
            'room_name' => $roomName,
            'password' => $password,
            'is_active' => true,
        ]);
    }
}

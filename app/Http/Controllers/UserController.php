<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\InviteUserRequest;
use App\Models\JanusRoom;
use App\Models\User;
use App\Services\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    public function getAllUsers()
    {
    try {
        // Get the authenticated user
        $authenticatedUser = Auth::user();

        // Get all users except the authenticated user
        $users = User::where('id', '!=', $authenticatedUser->id)->get();

        return $this->sendResponse("Successfully retrieved the users data", $users);
    } catch (Exception $e) {
        return $this->sendError($e->getMessage());
    }
    }

    public function inviteUser(InviteUserRequest $request)
    {
        $validated = $request->validated();

        try {

            // Retrieve user details from validated data
            $userEmail = $validated['user_email'];
            Log::info($userEmail);
            $userId = User::where('email', $userEmail)->select('id')->first()->id;
            Log::info($userId);
            $roomName = $validated['room_name'];
            $password = $validated['password'] ?? null;

            // Manually populate data for the CreateRoomRequest
            $createRoomData = new CreateRoomRequest();
            $createRoomData->merge(['room_name' => $roomName, 'password' => $password]);

            // Create room using the validated room data
            $janusController = new JanusController();
            // $janusController->createRoom($createRoomData);

            // Notify the user about the invitation
            $message = 'You have been invited to ' . $roomName;
            Log::info($message);
            $roomData = JanusRoom::where('owner_id', auth('sanctum')->id())->where('room_name', $roomName)->first();
            $roomId = $roomData->janus_room_id;
            Log::info($roomId);

            // Call Notification from controller and service - and store events in DB
            $notificationController = new NotificationController(new NotificationService);
            $notificationController->send($userId, $message, $roomName, $roomId);

            return $this->sendResponse('Notification Success', [
                'text' => $message,
                'room' => $roomData
            ]);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}

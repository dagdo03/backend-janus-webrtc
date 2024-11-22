<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\DeleteRoomRequest;
use App\Http\Requests\GetParticipantJanusRoomRequest;
use App\Models\JanusRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;
use RTippin\Janus\Janus;
use RTippin\Janus\Server;

class JanusController extends Controller
{
    private $server;
    private $janus;

    public function __construct()
    {
        $this->server = new Server();
        $this->server->setServerEndpoint(env('JANUS_SERVER_ENDPOINT'));
        $this->janus = new Janus($this->server);
    }

    public function createSession()
    {
        try {
            // Create a new Janus session
            $this->janus->connect()
                ->attach('janus.plugin.videoroom')
                ->message([
                    'request' => 'join',
                    'ptype' => 'publisher',
                    'room' => 1234
                ]);
            $response = $this->server->getApiResponse();
            return response()->json([
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create session: ' . $e->getMessage()], 500);
        }
    }

    public function createRoom(CreateRoomRequest $request)
    {

        $roomName = $request->input('room_name');
        $password = $request->input('password', null);
        $roomNumber = random_int(1, 999999);
        try {
            $this->janus->connect()
                ->attach('janus.plugin.videoroom')
                ->message([
                    'request' => 'create',
                    'room' => $roomNumber,
                    'permanent' => false,
                    'is_private' => false,
                    'publishers' => 12,
                    'description' => $roomName,
                    'password' => $password
                ]);

            // Log::info("user data" . auth('sanctum')->user());
            JanusRoom::create([
                'owner_id' => auth('sanctum')->id(),
                'room_name' => $roomName,
                'is_active' => true,
                'password' => $password,
                'janus_room_id' => $roomNumber
            ]);

            $response = $this->server->getApiResponse();
            return response()->json([
                'response' => $response,
                'message' => 'Room created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create room: ' . $e->getMessage()], 500);
        }
    }

    public function deleteRoom(DeleteRoomRequest $request){

        $roomId = $request->input('room_id');
        try {
            $this->janus->connect()
                ->attach('janus.plugin.videoroom')
                ->message([
                    'request' => 'destroy',
                    'room' => $roomId,
                ]);
            $janusRoom = JanusRoom::where('id', $roomId)->first();
            if ($janusRoom) {
                $janusRoom->delete();  // Delete the room record from the database
            }

            $response = $this->server->getApiResponse();
            return response()->json([
                'response' => $response,
                'message' => 'Room created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create room: ' . $e->getMessage()], 500);
        }
    }

    public function getRoomList()
    {
        try {
            $this->janus->connect()
                ->attach('janus.plugin.videoroom')
                ->message([
                    'request' => 'list'
                ]);
            $response = $this->server->getApiResponse();
            return response()->json([
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create session: ' . $e->getMessage()], 500);
        }
    }

    public function getParticipantList(GetParticipantJanusRoomRequest $request)
    {
        $roomId = $request->input('room_id');   
        $janusRoom = JanusRoom::where('id', $roomId)->first();
        try {
            $this->janus->connect()
                ->attach('janus.plugin.videoroom')
                ->message([
                    'request' => 'listparticipants',
                    'room' => (int)$roomId
                ]);
            $response = $this->server->getApiResponse();
            return response()->json([
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get participant list' . $e->getMessage()], 500);
        }
    }
}

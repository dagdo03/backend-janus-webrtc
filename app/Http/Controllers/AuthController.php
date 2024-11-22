<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends BaseController
{
    public function register(RegisterRequest $request){
        try{
            $request->validated($request->all());
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => "staff",
                'password' => Hash::make($request->password)
            ]);
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
        }
        catch(Exception $e){
             return response()->json([
            'success' => false,
            'message' => 'User created failed',
            'data' => $e->getMessage(),
        ], 400);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $expiration = Carbon::now()->addHour();
            $token = $user->createToken('token', [], $expiration)->plainTextToken;
            $userdata['token'] = $token;
            $user->update(["is_online" => true]);
            $userdata['expires_at'] = $expiration;
            $userdata['is_online'] = $user->is_online; 

            return $this->sendResponse("Login Success", $userdata);
        }
    } 

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
            $user->update(["is_online" => false]);
            return $this->sendResponse('Logout successful');
        }
        catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
 
    }

    public function getMe(){
        try{
            $user = Auth::user();
            return response()->json([
                'status' => true,
                'message' => "Successfully get current user",
                'data' => $user
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => "Error while getting current user",
                'data' => $e
            ]);
        }
    }
}

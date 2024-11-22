<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($message, $data = [], $code = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => (object) $data,
        ];

        return response()->json($response, $code);
    }

    public function sendError($error, $code = 404)
    {
        $response = [
            'status' => false,
            'message' => $error,
            'data' => new \stdClass()
        ];

        return response()->json($response, $code);
    }
}

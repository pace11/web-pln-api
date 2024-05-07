<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;

class ResponseController extends Controller
{
    /**
    * success response method.
    *
    * @return \Illuminate\Http\Response
    */

    public function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, $code);
    }

    /**
    * success response method.
    *
    * @return \Illuminate\Http\Response
    */

    public function sendResponsePagination($result, $message, $code = 200)
    {
        return response()->json($result, $code);
    }

    /**
    * return error response.
    *
    * @return \Illuminate\Http\Response
    */

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}

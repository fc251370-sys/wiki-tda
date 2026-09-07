<?php

namespace App\Response;

class ApiResponse{
    
    public static function success($message = 'Success', $code = 200, $data = [])
    {
        return response()->json([
            'message' => $message,
            'status' => $code,
            'data' => $data,
        ], $code);
    }

    public static function error($message, $code = 500)
    {
        return response()->json([
            'message' => $message,
            'status' => $code
        ], $code);
    }
}
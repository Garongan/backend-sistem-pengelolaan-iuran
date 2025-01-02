<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

class CommonResponse{
    public static function commonResponse(int $statusCode, string $message, array $data): JsonResponse
    {
        return response()->json([
            'statusCode' => $statusCode,
            'message' => $message,
            array_keys($data)[0] => array_values($data)[0]
        ], $statusCode);
    }
}
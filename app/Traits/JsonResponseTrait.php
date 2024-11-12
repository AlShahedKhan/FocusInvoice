<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;


trait JsonResponseTrait{
    protected function jsonResponse(int $status, string $message, $data = null, int $httpStatus = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $httpStatus);
    }
}

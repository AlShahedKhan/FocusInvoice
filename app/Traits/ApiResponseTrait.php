<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function ApiSendResponse($message, $status, $data = null)
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data
        ]);
    }
}

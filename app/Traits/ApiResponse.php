<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

trait ApiResponse
{

    protected function makeResponse(bool $status, array $data, string $message): array
    {
        return [
            'status'  => $status,
            'data'    => $data,
            'message' => $message,
        ];
    }


    protected function successResponse(array $data = [], string $message = '', int $statusCode = 200): JsonResponse {

        return Response::json(
            $this->makeResponse(true, $data, $message),
            $statusCode
        );
    }


    protected function errorResponse(string $message, int $statusCode = 400, array $data = []): JsonResponse {
        
        return Response::json(
            $this->makeResponse(false, $data, $message),
            $statusCode
        );
    }
}

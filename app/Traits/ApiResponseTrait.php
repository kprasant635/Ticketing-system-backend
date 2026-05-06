<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    public function success(mixed $data, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $data
        ], $statusCode);
    }

    public function error(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $statusCode);
    }

    public function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }

    public function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], 404);
    }

    public function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], 401);
    }

    public function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], 403);
    }

    public function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], 500);
    }
}
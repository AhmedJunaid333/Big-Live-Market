<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

trait ApiResponse
{
    /**
     * Return a standardized successful JSON response.
     */
    protected function successResponse(mixed $data = null, ?string $message = null, int $statusCode = 200, array $meta = []): JsonResponse
    {
        $payload = [];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $statusCode);
    }

    /**
     * Return a standardized collection/cursor paginated response.
     */
    protected function collectionResponse(mixed $items, ?string $nextCursor = null, int $statusCode = 200, array $extraMeta = []): JsonResponse
    {
        return response()->json([
            'data' => $items,
            'meta' => array_merge([
                'next_cursor' => $nextCursor,
            ], $extraMeta),
        ], $statusCode);
    }

    /**
     * Return a standardized error response according to Big Live Marketplace specification.
     */
    protected function errorResponse(string $message, string $code = 'BAD_REQUEST', array $errors = [], int $statusCode = 400): JsonResponse
    {
        $payload = [
            'message' => $message,
            'code' => $code,
            'request_id' => (string) Str::uuid(),
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $statusCode);
    }
}

<?php

use Illuminate\Http\JsonResponse;


if (! function_exists('json')) {
    /**
     * @param string $message
     * @param array<mixed> $data
     * @param int $statusCode
     * @param array<mixed> $headers
     * @param int $options
     */
    function json(string $message, array|object $data = [], int $statusCode = 200, array $headers = [], int $options = 0): JsonResponse
    {
        return response()->json([
            'status' => str($statusCode)->startsWith('2') ? 'success' : 'error',
            'message' => $message,
            'data' => $data,
        ], $statusCode, $headers, $options);
    }
}

if (! function_exists('json_message')) {
    /**
     * @param string $message
     * @param int $statusCode
     * @param array<mixed> $headers
     * @param int $options
     */
    function json_message(string $message, int $statusCode = 200, array $headers = [], int $options = 0): JsonResponse
    {
        return json($message, [], $statusCode, $headers, $options);
    }
}

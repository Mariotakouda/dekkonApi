<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json(array_filter([
            'success' => true,
            'message' => $message ?: null,
            'data' => $data,
        ], fn ($v) => ! is_null($v)) + ['success' => true], $status);
    }

    public static function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json(array_filter([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], fn ($v) => ! is_null($v)), $status);
    }

    public static function paginated(mixed $paginator, string $resourceClass, string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?: null,
            'data' => $resourceClass::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}

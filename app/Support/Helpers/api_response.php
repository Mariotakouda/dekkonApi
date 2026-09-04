<?php

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

if (! function_exists('api_success')) {
    function api_success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $status);
    }
}

if (! function_exists('api_error')) {
    function api_error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return ApiResponse::error($message, $status, $errors);
    }
}

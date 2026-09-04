<?php

namespace App\Traits;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

trait ApiResponder
{
    protected function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $status);
    }

    protected function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return ApiResponse::error($message, $status, $errors);
    }

    protected function paginated(mixed $paginator, string $resourceClass, string $message = ''): JsonResponse
    {
        return ApiResponse::paginated($paginator, $resourceClass, $message);
    }
}

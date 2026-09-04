<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->status !== UserStatus::ACTIVE || ! $user->is_active) {
            return ApiResponse::error('Compte inactif ou suspendu.', 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Usage : ->middleware('role:ADMIN|ORDER_MANAGER')
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role) {
            return ApiResponse::error('Rôle insuffisant.', 403);
        }

        $required = explode('|', $roles);

        if (! in_array($user->role->code, $required, true)) {
            return ApiResponse::error('Rôle insuffisant.', 403);
        }

        return $next($request);
    }
}

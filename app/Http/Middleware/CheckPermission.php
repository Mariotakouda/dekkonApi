<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Usage dans les routes : ->middleware('permission:orders.confirm')
     * Plusieurs permissions séparées par | = suffisant d'en avoir une seule.
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error('Non authentifié.', 401);
        }

        $required = explode('|', $permissions);

        foreach ($required as $code) {
            if ($user->hasPermission($code)) {
                return $next($request);
            }
        }

        return ApiResponse::error('Permission insuffisante.', 403);
    }
}

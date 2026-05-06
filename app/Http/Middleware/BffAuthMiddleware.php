<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class BffAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $userId = $request->header('x-user-id');
        $role = $request->header('x-user-role');

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Attach manually
        $request->merge([
            'bff_user' => [
                'id' => $userId,
                'role' => $role
            ]
        ]);

        return $next($request);
    }
}

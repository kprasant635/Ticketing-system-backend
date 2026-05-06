<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Closure;
use Exception;

class JwtAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return response()->json(['message' => 'Token missing'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $secret = env('JWT_SECRET');  // must match your token generator

            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Attach user info
            $request->attributes->add([
                'user' => $decoded
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Invalid token',
                'error' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}

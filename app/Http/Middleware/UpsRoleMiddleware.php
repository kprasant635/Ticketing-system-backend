<?php

namespace App\Http\Middleware;

use App\Core\Standards\ApiResponseLibrary;
use App\Traits\UpsApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Closure;

class UpsRoleMiddleware
{
    use UpsApiTrait, ApiResponseLibrary;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->respondWithProblem(
                title: 'Unauthorized',
                detail: 'Token missing',
                httpStatus: 401,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }

        // Cache the postings for this token to avoid hitting the API on every request
        // Using a cache time of 15 minutes, hashed token as cache key
        $cacheKey = 'ups_postings_' . hash('sha256', $token);

        $myPostings = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($token) {
            try {
                $response = $this->upsGet('runtime/my-postings', [], [
                    'Authorization' => 'Bearer ' . $token
                ]);

                if ($response && $response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                \Log::error('UPS Role Middleware Error: ' . $e->getMessage());
            }
            return null;
        });

        if (!$myPostings || !isset($myPostings['data']) || empty($myPostings['data'])) {
            return $this->respondWithProblem(
                title: 'Unauthorized',
                detail: 'Invalid token or no postings found',
                httpStatus: 401,
                errorCode: 'ELRS-VAL-INVALIDID'
            );
        }

        // Extract all role codes from the postings
        $userRoles = [];
        foreach ($myPostings['data'] as $postingData) {
            if (isset($postingData['posting']['role']['role_code'])) {
                $userRoles[] = $postingData['posting']['role']['role_code'];
            }
        }

        // If roles are provided as middleware parameters, check if the user has any of them
        if (!empty($roles)) {
            $hasRole = false;
            foreach ($roles as $role) {
                if (in_array($role, $userRoles)) {
                    $hasRole = true;
                    break;
                }
            }

            if (!$hasRole) {
                return $this->respondWithProblem(
                    title: 'Forbidden',
                    detail: 'You do not have the required role',
                    httpStatus: 403,
                    errorCode: 'ELRS-VAL-INVALIDID'
                );
            }
        }

        // Attach the roles and postings to the request for use in controllers
        $request->attributes->add([
            'ups_roles' => $userRoles,
            'ups_postings' => $myPostings['data']
        ]);

        return $next($request);
    }
}

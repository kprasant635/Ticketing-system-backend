<?php

namespace App\Http\Middleware;

use App\Core\Standards\ApiResponseLibrary;
use App\Models\User;
use App\Traits\UpsApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 🚀 Auto-login the user based on token 'sub' (Keycloak/UPS ID) or 'email'
        try {
            $parts = explode('.', $token);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode($parts[1]), true);
                $upsUserUuid = $payload['sub'] ?? null;
                $email = $payload['email'] ?? null;

                $user = null;
                if ($upsUserUuid) {
                    $user = User::where('ups_user_uuid', $upsUserUuid)->first();
                }

                if (!$user && $email) {
                    $user = User::where('email', $email)->first();
                    // If found by email, sync the UUID for future requests
                    if ($user && $upsUserUuid) {
                        $user->update(['ups_user_uuid' => $upsUserUuid]);
                    }
                }

                if ($user) {
                    Auth::login($user);
                } else {
                    \Log::warning('UpsRoleMiddleware: No local user found for sub=' . ($upsUserUuid ?? 'N/A') . ' email=' . ($email ?? 'N/A'));

                    // 🚀 STEP 1: If user not found, try to provision them from UPS
                    try {
                        $user = $this->syncUpsUser($token, $upsUserUuid);
                        if ($user) {
                            \Log::info('UpsRoleMiddleware: Successfully provisioned user sub=' . $upsUserUuid);
                            Auth::login($user);
                        }
                    } catch (\Exception $e) {
                        \Log::error('UpsRoleMiddleware Provisioning Error: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('UpsRoleMiddleware Auth Error: ' . $e->getMessage());
        }

        // Cache the postings for this token to avoid hitting the API on every request
        // Using a cache time of 15 minutes, hashed token as cache key
        $cacheKey = 'ups_postings_v2_' . hash('sha256', $token);

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
                $userRoles[] = strtolower($postingData['posting']['role']['role_code']);
            }
        }

        // \Log::info('Required Roles:', $roles);
        // \Log::info('User Roles:', $userRoles);

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
                \Log::warning('403 Forbidden: User does not have required role. Required: ' . implode(',', $roles) . ' | User has: ' . implode(',', $userRoles));
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


<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait UpsApiTrait
{
    /**
     * Get the base URL for the UPS Service.
     *
     * @return string
     */
    protected function getUpsBaseUrl(): string
    {
        return env('UPS_API_BASE_URL', 'https://elrs-services.assam.gov.in/ups-service/ups/api/v1');
    }

    /**
     * Make a POST request to the UPS API.
     *
     * @param string $endpoint
     * @param array $payload
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     * @throws \Exception
     */
    protected function upsPost(string $endpoint, array $payload = [], array $headers = [])
    {
        $url = $this->getUpsBaseUrl() . '/' . ltrim($endpoint, '/');

        $defaultHeaders = [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders(array_merge($defaultHeaders, $headers))
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::error('UPS API POST Request Failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload' => $payload,
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('UPS API POST Request Exception', [
                'url' => $url,
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);
            throw $e;
        }
    }

    /**
     * Make a GET request to the UPS API.
     *
     * @param string $endpoint
     * @param array $query
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     * @throws \Exception
     */
    protected function upsGet(string $endpoint, array $query = [], array $headers = [])
    {
        $url = $this->getUpsBaseUrl() . '/' . ltrim($endpoint, '/');

        $defaultHeaders = [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders(array_merge($defaultHeaders, $headers))
                ->get($url, $query);

            if (!$response->successful()) {
                Log::error('UPS API GET Request Failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'query' => $query,
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('UPS API GET Request Exception', [
                'url' => $url,
                'message' => $e->getMessage(),
                'query' => $query,
            ]);
            throw $e;
        }
    }

    protected function upsGet_user_details(string $endpoint, array $query = [], array $headers = [])
    {
        $url = $this->getUpsBaseUrl() . '/' . ltrim($endpoint, '/');

        $defaultHeaders = [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders(array_merge($defaultHeaders, $headers))
                ->get($url, $query);

            if (!$response->successful()) {
                Log::error('UPS API GET Failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('UPS API Exception', [
                'url' => $url,
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sync/Provision a user from UPS if they don't exist locally.
     */
    public function syncUpsUser(string $token, string $upsUserUuid): ?User
    {
        try {
            // 1. Fetch user details from UPS
            $response = $this->upsGet_user_details('master/users/' . $upsUserUuid, [], [
                'Authorization' => 'Bearer ' . $token
            ]);

            if (!$response || !$response->successful()) {
                Log::error('UPS Sync: Failed to fetch user details from UPS for sub=' . $upsUserUuid);
                return null;
            }

            $details = $response->json()['data'] ?? null;
            if (!$details)
                return null;

            // 2. Fetch postings to get roles
            $postingsResponse = $this->upsGet('runtime/my-postings', [], [
                'Authorization' => 'Bearer ' . $token
            ]);

            $roles = [];
            if ($postingsResponse && $postingsResponse->successful()) {
                $postings = $postingsResponse->json()['data'] ?? [];
                foreach ($postings as $p) {
                    if (!empty($p['is_current'])) {
                        $roleCode = $p['posting']['role']['role_code'] ?? null;
                        if ($roleCode)
                            $roles[] = strtolower($roleCode);
                    }
                }
            }
            $roles = array_unique($roles);

            // 3. Create or Update user
            return User::updateOrCreate(
                ['ups_user_uuid' => $details['user_uuid'], 'ups_user_id' => $upsUserUuid],
                [
                    'name' => $details['full_name'] ?? 'UPS User',
                    'email' => $details['email'] ?? null,
                    'employee_code' => $details['employee_code'] ?? null,
                    'designation' => $details['designation_name'] ?? null,
                    'phone' => $details['mobile_no'] ?? null,
                    'role_name' => json_encode($roles),
                    'status' => ($details['status'] ?? 1) == 1 ? 'active' : 'inactive',
                    'password' => bcrypt('ups_sync_password_' . uniqid()),
                ]
            );
        } catch (\Exception $e) {
            Log::error('UPS Sync Exception: ' . $e->getMessage());
            return null;
        }
    }
}

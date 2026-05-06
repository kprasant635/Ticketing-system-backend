<?php

namespace App\Traits;

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
}

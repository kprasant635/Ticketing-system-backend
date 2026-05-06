<?php

namespace App\Core\Standards;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Trait ApiResponseLibrary
 * 
 * The authoritative implementation of ELRS API standards.
 * Developers should use this trait in Controllers to ensure platform-wide consistency.
 */
trait ApiResponseLibrary
{
    /**
     * Respond with a standardized success envelope.
     * 
     * @param mixed $data The resource payload
     * @param string $message User-friendly confirmation
     * @param string $code Machine-readable status (from ResponseStatus)
     * @param int $httpStatus Protocol-level status code
     * @param array $metadata Out-of-band operational data
     * @return JsonResponse
     */
    public function respondWithSuccess(
        $data = [], 
        string $message = "Operation successful", 
        string $code = ResponseStatus::OK, 
        int $httpStatus = 200, 
        array $metadata = []
    ): JsonResponse {
        return response()->json([
            "status" => "success",
            "code" => $code,
            "message" => $message,
            "timestamp" => now()->toIso8601String(),
            "instance" => $this->getTraceUrn(),
            "data" => $data,
            "metadata" => $metadata
        ], $httpStatus);
    }

    /**
     * Respond with an RFC 9457 compliant error (Problem Detail).
     * 
     * @param string $title Short, human-readable summary of the problem
     * @param string $detail Detailed explanation of the error
     * @param int $httpStatus HTTP status code (400-599)
     * @param string $errorCode ELRS-specific 4-digit error code
     * @param array $metadata Additional context (like validation violations)
     * @return JsonResponse
     */
    public function respondWithProblem(
        string $title,
        string $detail,
        int $httpStatus = 400,
        string $errorCode = "ELRS-SYS-1000",
        array $metadata = []
    ): JsonResponse {
        $traceId = $this->getTraceId();

        // Production Masking Logic
        // If it's a 500 level error, we mask the sensitive 'detail' in production.
        if (app()->environment('production') && $httpStatus >= 500) {
            $realDetail = $detail;
            $detail = "An internal server error occurred. Please contact support and provide the instance ID.";
            
            // Log the real error for internal debugging
            Log::error("[{$errorCode}] Internal Fault: {$realDetail}", [
                'trace_id' => $traceId,
                'status' => $httpStatus
            ]);
        }

        $responseBody = [
            "type" => "https://api.elrs.gov.in/probs/" . Str::slug($title),
            "title" => $title,
            "status" => $httpStatus,
            "errorCode" => $errorCode,
            "detail" => $detail,
            "instance" => "urn:uuid:" . $traceId,
            "timestamp" => now()->toIso8601String()
        ];

        if (!empty($metadata)) {
            $responseBody["metadata"] = $metadata;
        }

        return response()->json($responseBody, $httpStatus)
                         ->header('Content-Type', 'application/problem+json')
                         ->header('X-Trace-Id', $traceId);
    }

    /**
     * Respond with a 207 Multi-Status for bulk operations.
     * 
     * @param array $summary Summary counts (requested, success, failed)
     * @param array $details Individual record statuses
     * @return JsonResponse
     */
    public function respondWithPartialSuccess(array $summary, array $details): JsonResponse
    {
        return $this->respondWithSuccess(
            ['summary' => $summary, 'details' => $details],
            "Bulk operation completed with partial results",
            ResponseStatus::PARTIAL_SUCCESS,
            207
        );
    }

    /**
     * Get the formatted Trace URN.
     * 
     * @return string
     */
    protected function getTraceUrn(): string
    {
        return "urn:uuid:" . $this->getTraceId();
    }

    /**
     * Get or Generate the Trace ID for this request.
     * 
     * @return string
     */
    protected function getTraceId(): string
    {
        return request()->header('X-Trace-Id', (string) Str::uuid());
    }
}

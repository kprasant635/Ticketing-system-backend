<?php

namespace App\Exceptions;

use App\Core\Standards\ApiResponseLibrary;
use App\Core\Standards\ResponseStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseLibrary;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // Handle API requests
        if ($request->is('api/*')) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions with proper error responses
     */
    private function handleApiException($request, Throwable $exception)
    {
        $action = $request->route() ? $request->route()->getActionName() : '';
        $method = 'UNKNOWN';
        
        if ($action && str_contains($action, '@')) {
            $method = strtoupper(last(explode('@', $action)));
        } elseif (is_string($action) && $action !== 'Closure') {
            $method = strtoupper(class_basename($action));
        }

        // Model Not Found
        if ($exception instanceof ModelNotFoundException) {
            return $this->respondWithProblem(
                title: 'Resource Not Found',
                detail: 'The requested resource was not found.',
                httpStatus: 404,
                errorCode: "ELRS-SYS-4040-{$method}"
            );
        }

        // Route Not Found
        if ($exception instanceof NotFoundHttpException) {
            return $this->respondWithProblem(
                title: 'Endpoint Not Found',
                detail: 'The requested API endpoint does not exist.',
                httpStatus: 404,
                errorCode: "ELRS-SYS-4041"
            );
        }

        // Validation Errors
        if ($exception instanceof ValidationException) {
            return $this->respondWithProblem(
                title: 'Validation failed',
                detail: 'One or more fields are invalid.',
                httpStatus: 422,
                errorCode: "ELRS-VAL-{$method}",
                metadata: ['violations' => $exception->errors()]
            );
        }

        // Authentication
        if ($this->isAuthenticationException($exception)) {
            return $this->respondWithProblem(
                title: 'Unauthenticated',
                detail: 'Valid authentication credentials are required.',
                httpStatus: 401,
                errorCode: "ELRS-SEC-4010-{$method}"
            );
        }

        // Authorization
        if ($this->isAuthorizationException($exception)) {
            return $this->respondWithProblem(
                title: 'Forbidden',
                detail: 'You do not have permission to access this resource.',
                httpStatus: 403,
                errorCode: "ELRS-SEC-4030-{$method}"
            );
        }

        // Generic server error
        return $this->respondWithProblem(
            title: 'Internal Server Error',
            detail: $exception->getMessage(),
            httpStatus: 500,
            errorCode: "ELRS-SYS-5000-{$method}"
        );
    }

    /**
     * Check if exception is an authentication exception
     */
    private function isAuthenticationException(Throwable $exception): bool
    {
        return in_array(
            get_class($exception),
            [
                'Illuminate\\Auth\\AuthenticationException',
                'Illuminate\\Auth\\TokenMismatchException'
            ]
        );
    }

    /**
     * Check if exception is an authorization exception
     */
    private function isAuthorizationException(Throwable $exception): bool
    {
        return get_class($exception) === 'Illuminate\\Auth\\Access\\AuthorizationException';
    }
}

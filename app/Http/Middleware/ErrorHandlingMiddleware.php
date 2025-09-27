<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ErrorHandlingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Add request ID for tracking
        $requestId = $request->header('X-Request-ID') ?? uniqid();
        $request->headers->set('X-Request-ID', $requestId);

        // Log incoming request
        $this->logIncomingRequest($request, $requestId);

        $startTime = microtime(true);

        try {
            $response = $next($request);

            // Log successful response
            $this->logResponse($request, $response, $startTime, $requestId);

            return $response;

        } catch (\Throwable $exception) {
            // Log the exception
            $this->logException($request, $exception, $startTime, $requestId);

            // Re-throw the exception to let the handler deal with it
            throw $exception;
        }
    }

    /**
     * Log incoming request details
     */
    protected function logIncomingRequest(Request $request, string $requestId): void
    {
        $logData = [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        // Log request payload for POST/PUT/PATCH (excluding sensitive data)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $payload = $request->except([
                'password',
                'password_confirmation',
                'token',
                '_token',
                'api_token',
                'secret',
                'key',
                'credit_card',
                'cvv',
            ]);

            if (!empty($payload)) {
                $logData['payload'] = $payload;
            }
        }

        // Log query parameters
        if (!empty($request->query())) {
            $logData['query_params'] = $request->query();
        }

        Log::info('Incoming Request', $logData);
    }

    /**
     * Log response details
     */
    protected function logResponse(Request $request, $response, float $startTime, string $requestId): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $logData = [
            'request_id' => $requestId,
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'memory_usage_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'user_id' => auth()->id(),
        ];

        // Log response size for large responses
        $contentLength = $response->headers->get('Content-Length');
        if ($contentLength && $contentLength > 1024 * 1024) { // > 1MB
            $logData['response_size_mb'] = round($contentLength / 1024 / 1024, 2);
        }

        // Determine log level based on status code
        if ($response->getStatusCode() >= 500) {
            Log::error('Response - Server Error', $logData);
        } elseif ($response->getStatusCode() >= 400) {
            Log::warning('Response - Client Error', $logData);
        } elseif ($duration > 5000) { // Slow response > 5 seconds
            Log::warning('Response - Slow Response', $logData);
        } else {
            Log::info('Response - Success', $logData);
        }
    }

    /**
     * Log exception details
     */
    protected function logException(Request $request, \Throwable $exception, float $startTime, string $requestId): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $logData = [
            'request_id' => $requestId,
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'duration_ms' => $duration,
            'memory_usage_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'user_id' => auth()->id(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
        ];

        // Add stack trace for development
        if (config('app.debug')) {
            $logData['trace'] = $exception->getTraceAsString();
        }

        // Add request context for debugging
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $payload = $request->except([
                'password',
                'password_confirmation',
                'token',
                '_token',
                'api_token',
                'secret',
                'key',
                'credit_card',
                'cvv',
            ]);

            if (!empty($payload)) {
                $logData['request_payload'] = $payload;
            }
        }

        Log::error('Request Exception', $logData);
    }
}
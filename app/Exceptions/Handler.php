<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'token',
        '_token',
        'api_token',
        'secret',
        'key',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Custom logging for different exception types
            if ($e instanceof QueryException) {
                Log::error('Database Query Error', [
                    'message' => $e->getMessage(),
                    'sql' => $e->getSql() ?? 'Unknown query',
                    'bindings' => $e->getBindings() ?? [],
                    'trace' => $e->getTraceAsString(),
                    'user_id' => auth()->id(),
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                ]);
            }

            if ($e instanceof \Exception && !($e instanceof ValidationException)) {
                Log::error('Application Error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => auth()->id(),
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle AJAX requests
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        // Handle specific exceptions for web requests
        if ($exception instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($request, $exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->view('errors.405', [], 405);
        }

        if ($exception instanceof TokenMismatchException) {
            return redirect()->back()
                ->withInput($request->except($this->dontFlash))
                ->withErrors(['token' => 'Security token expired. Please try again.']);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->view('errors.403', [
                'message' => $exception->getMessage() ?: 'Access denied.'
            ], 403);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return redirect()->back()
                ->withErrors(['throttle' => 'Too many requests. Please try again later.']);
        }

        if ($exception instanceof QueryException) {
            return $this->handleDatabaseException($request, $exception);
        }

        // Handle other HTTP exceptions
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $view = "errors.{$statusCode}";

            if (view()->exists($view)) {
                return response()->view($view, [
                    'message' => $exception->getMessage()
                ], $statusCode);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions and return JSON responses
     */
    protected function handleApiException($request, Throwable $exception)
    {
        $statusCode = 500;
        $message = 'An error occurred while processing your request.';
        $errors = null;

        if ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'The given data was invalid.';
            $errors = $exception->errors();
        } elseif ($exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'The requested resource was not found.';
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Authentication required.';
        } elseif ($exception instanceof AuthorizationException) {
            $statusCode = 403;
            $message = 'Access denied.';
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'The requested endpoint was not found.';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $message = 'Method not allowed for this endpoint.';
        } elseif ($exception instanceof ThrottleRequestsException) {
            $statusCode = 429;
            $message = 'Too many requests. Please try again later.';
        } elseif ($exception instanceof QueryException) {
            $statusCode = 500;
            $message = 'A database error occurred.';
            if (config('app.debug')) {
                $message .= ' Details: ' . $exception->getMessage();
            }
        } elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'HTTP error occurred.';
        }

        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        if (config('app.debug') && !($exception instanceof ValidationException)) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->take(5)->toArray(),
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Handle model not found exceptions
     */
    protected function handleModelNotFoundException($request, ModelNotFoundException $exception)
    {
        $model = strtolower(class_basename($exception->getModel()));

        return response()->view('errors.404', [
            'message' => "The requested {$model} could not be found."
        ], 404);
    }

    /**
     * Handle database exceptions
     */
    protected function handleDatabaseException($request, QueryException $exception)
    {
        $errorCode = $exception->errorInfo[1] ?? null;

        // Handle common database errors
        switch ($errorCode) {
            case 1062: // Duplicate entry
                $message = 'This record already exists. Please check your input and try again.';
                break;
            case 1451: // Foreign key constraint fails
                $message = 'Cannot delete this record because it is being used by other data.';
                break;
            case 1452: // Foreign key constraint fails on insert/update
                $message = 'Invalid reference to related data. Please check your selection.';
                break;
            default:
                $message = 'A database error occurred. Please try again.';
                if (config('app.debug')) {
                    $message .= ' Error: ' . $exception->getMessage();
                }
        }

        return redirect()->back()
            ->withInput($request->except($this->dontFlash))
            ->withErrors(['database' => $message]);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
                'status_code' => 401
            ], 401);
        }

        $redirectTo = '/login';

        // Check if we have a custom login route
        if (route('login', [], false)) {
            $redirectTo = route('login');
        }

        return redirect()->guest($redirectTo)
            ->withErrors(['auth' => 'Please log in to access this page.']);
    }
}
<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        // Add custom log levels if needed.
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        // Add exceptions that should not be reported.
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        // Custom JSON response for resource not found errors.
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'error'   => 'Resource Not Found',
                'message' => 'The requested resource was not found',
            ], 404);
        });

        $this->reportable(function (Throwable $e) {
            // You may add any reporting logic here.
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception): Response
    {
        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            return response()->json([
                'error'   => 'Resource Not Found',
                'message' => 'The requested resource was not found',
            ], 404);
        }

        if ($exception instanceof AuthenticationException && $request->expectsJson()) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AuthorizationException && $request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($exception instanceof ValidationException && $request->expectsJson()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors'  => $exception->errors(),
            ], 422);
        }

        if ($exception instanceof MethodNotAllowedHttpException && $request->expectsJson()) {
            return response()->json(['message' => 'Method Not Allowed'], 405);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // If the request does not expect JSON, you may define a redirect if needed.
        return redirect()->guest(route('login'));
    }
}

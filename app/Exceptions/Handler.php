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
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
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
            // Add reporting logic here if needed.
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
        // Handle ModelNotFoundException or NotFoundHttpException
        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            return response()->json([
                'error'   => 'Resource Not Found',
                'message' => 'The requested resource was not found',
            ], 404);
        }

        // Handle AuthenticationException
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        // Handle AuthorizationException
        if ($exception instanceof AuthorizationException && $request->expectsJson()) {
            return response()->json(['message' => 'You are unauthorised to make this request.'], 403);
        }

        // Handle ValidationException
        if ($exception instanceof ValidationException && $request->expectsJson()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors'  => $exception->errors(),
            ], 422);
        }

        // Handle MethodNotAllowedHttpException
        if ($exception instanceof MethodNotAllowedHttpException && $request->expectsJson()) {
            return response()->json(['message' => 'Method Not Allowed'], 405);
        }

        // Handle RouteNotFoundException (specific to your issue)
        if ($exception instanceof RouteNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'Route Not Found',
                    'message' => 'The requested route was not found',
                ], 404);
            }
            // For non-JSON requests, return a fallback response or redirect
            return response()->view('errors.404', [], 404); // Ensure you have a 404 view, or adjust this
        }

        // Fallback to parent handler for unhandled exceptions
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a JSON response or redirect.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'You are unauthenticated, please login again.'], 401);
        }

        // Check if the 'login' route exists before redirecting
        if (!\Route::has('login')) {
            // Fallback response if 'login' route is not defined
            return response()->view('errors.unauthenticated', [], 401); // Create this view or adjust
        }

        return redirect()->guest(route('login'));
    }
}
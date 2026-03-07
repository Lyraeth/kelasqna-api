<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Error Handling

        /**
         * 401 - Unauthenticated
         */
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        });

        /**
         * 404 - Route tidak ditemukan
         */
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => true,
                    'message' => 'Resource not found',
                ], 404);
            }
        });

        /**
         * 405 - Method tidak diizinkan
         */
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => true,
                    'message' => 'Method not allowed',
                ], 405);
            }
        });

        /**
         * 422 - Validation Error
         */
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {

                $firstError = collect($e->errors())
                    ->flatten()
                    ->first();

                return response()->json([
                    'error' => true,
                    'message' => $firstError ?? 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        /**
         * HTTP Exception umum (403, 409, dll)
         */
        $exceptions->render(function (HttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage() ?: 'HTTP error',
                ], $e->getStatusCode());
            }
        });

        /**
         * Fallback - 500 Internal Server Error
         */
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {

                // Optional: log error untuk debugging internal
                logger()->error($e);

                return response()->json([
                    'error' => true,
                    'message' => app()->environment('production')
                        ? 'Internal Server Error'
                        : $e->getMessage(),
                ], 500);
            }
        });
    })
    ->create();

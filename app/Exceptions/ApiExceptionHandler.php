<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    public static function render(Throwable $exception)
    {
        if ($exception instanceof QueryException) {
            return response()->json([
                'message' => 'Internal server error',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Resource not found',
                'data' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'message' => 'Method not allowed',
                'data' => [],
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'data' => [],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Fallback: unexpected error
        return response()->json([
            'message' => 'An unexpected error occurred',
            'data' => [],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

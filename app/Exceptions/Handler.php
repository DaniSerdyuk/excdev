<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * @param           $request
     * @param Throwable $e
     *
     * @return JsonResponse
     */
    public function render($request, Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof AuthenticationException => ApiResponse::error(
                'AuthenticationException',
                $e->getMessage(),
                'Guard: ' . implode(', ', $e->guards()),
                Response::HTTP_UNAUTHORIZED,
            ),

            $e instanceof AuthorizationException => ApiResponse::error(
                'AuthorizationException',
                $e->getMessage(),
                '',
                Response::HTTP_FORBIDDEN,
            ),

            $e instanceof ValidationException => $this->validateExceptionResponse($e),

            $e instanceof ModelNotFoundException => ApiResponse::error(
                'ModelNotFoundException',
                $e->getMessage(),
                '',
                Response::HTTP_NOT_FOUND,
            ),

            $e instanceof \PDOException => ApiResponse::error(
                'PDOException',
                'Bad Request',
                'Error executing SQL query',
                Response::HTTP_BAD_REQUEST,
            ),

            $e instanceof BadRequestHttpException => ApiResponse::error(
                class_basename($e),
                $e->getMessage(),
                '',
                Response::HTTP_BAD_REQUEST,
            ),

            $e->getCode() === 500 => ApiResponse::error(
                'InternalServerError',
                'Unknown error occurred. Try to refresh the page and repeat actions.',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ),

            $e->getCode() === 400 => ApiResponse::error(
                class_basename($e),
                $e->getMessage(),
                '',
                $e->getCode(),
            ),

            $e instanceof HttpResponseException => ApiResponse::error(
                'HttpResponseException',
                $e->getMessage(),
                '',
                $e->getCode(),
            ),

            $e instanceof NotFoundHttpException && $request->is('/') => ApiResponse::error(
                'NotFoundHttpException',
                'Not Found',
                '',
                404,
            ),

            $e instanceof \TypeError => $this->getTypeErrorResponse($e),

            default => ApiResponse::error(
                class_basename($e),
                $e->getMessage(),
                '',
                method_exists($e, 'getStatusCode') ? $e->getStatusCode() : ($e->getCode() <= 0 ? 500 : $e->getCode()),
            ),
        };
    }

    /**
     * @param ValidationException $e
     *
     * @return JsonResponse
     */
    protected function validateExceptionResponse(ValidationException $e): JsonResponse
    {
        return ApiResponse::error(
            'ValidationException',
            'Validation Error',
            $e->validator->errors(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /**
     * @param \TypeError $e
     *
     * @return JsonResponse
     */
    protected function getTypeErrorResponse(\TypeError $e): JsonResponse
    {
        return ApiResponse::error(
            'TypeError',
            'Incorrect input type',
            $e->getMessage(),
            Response::HTTP_BAD_REQUEST,
        );
    }
}

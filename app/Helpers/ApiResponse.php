<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ApiResponse
{
    /**
     * @param mixed $data
     * @param int   $code
     * @param int   $options
     *
     * @return JsonResponse
     */
    public static function success(mixed $data = [], int $code = Response::HTTP_OK, int $options = 0): JsonResponse
    {
        return new JsonResponse(data: $data, status: $code, options: $options);
    }

    /**
     * @param string|object $type
     * @param string        $message
     * @param string|array  $description
     * @param int           $code
     *
     * @return JsonResponse
     */
    public static function error(
        string|object $type,
        string $message,
        string|array $description,
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        $response = [
            'error' => [
                'type' => $type,
                'message' => $message,
                'description' => $description,
                'code' => $code,
            ],
        ];

        return new JsonResponse(data: $response, status: $code);
    }
}

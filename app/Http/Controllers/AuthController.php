<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service)
    {
    }

    /**
     * @param LoginRequest $request
     *
     * @throws \Illuminate\Auth\AuthenticationException
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return ApiResponse::success($this->service->login($request->validated()));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return ApiResponse::success((bool)$user->tokens()->delete());
    }
}

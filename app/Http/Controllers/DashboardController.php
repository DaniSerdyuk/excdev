<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $service
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function getUserWithBalance(): JsonResponse
    {
        /** @var User $auth */
        $auth = Auth::user();

        return ApiResponse::success(
            $this->service->getUserWithBalance($auth)
        );
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getTransactions(int $id, Request $request): JsonResponse
    {
        return ApiResponse::success(
            $this->service->getTransactions(
                $id,
                $request->get('perPage', 10),
                $request->get('page', 1),
                $request->get('sort', 'asc'),
                $request->get('search')
            ));
    }
}

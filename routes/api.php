<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/user-and-balance', [DashboardController::class, 'getUserWithBalance']);
    Route::get('/{id}/transactions', [DashboardController::class, 'getTransactions']);
});

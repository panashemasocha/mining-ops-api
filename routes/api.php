<?php

use App\Http\Controllers\V1\JobPositionController;
use App\Http\Controllers\V1\DriverInfoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\DepartmentController;
use App\Http\Controllers\V1\BranchController;
use App\Http\Controllers\V1\UserRoleController;


Route::prefix('v1')->group(function () {
    //Public end points
    Route::post('login', [AuthController::class, 'login']);

    //Private end points
    Route::middleware('auth:sanctum')->group(function () {
        //Auth end points
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        //User end points
        Route::apiResource('users', UserController::class);
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('branches', BranchController::class);
        Route::apiResource('roles', UserRoleController::class);
        Route::apiResource('driver-info', DriverInfoController::class);
        Route::apiResource('job-positions', JobPositionController::class);
    });
});


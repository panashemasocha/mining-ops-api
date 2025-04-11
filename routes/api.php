<?php

use App\Http\Controllers\ConsolidatedDataController;
use App\Http\Controllers\V1\AssignedVehicleController;
use App\Http\Controllers\V1\CostPriceController;
use App\Http\Controllers\V1\DispatchController;
use App\Http\Controllers\V1\JobPositionController;
use App\Http\Controllers\V1\DriverInfoController;
use App\Http\Controllers\V1\OreController;
use App\Http\Controllers\V1\SupplierController;
use App\Http\Controllers\V1\TripController;
use App\Http\Controllers\V1\VehicleController;
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
        //Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        //User
        Route::apiResource('users', UserController::class);
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('branches', BranchController::class);
        Route::apiResource('roles', UserRoleController::class);
        Route::apiResource('driver-info', DriverInfoController::class);
        Route::apiResource('job-positions', JobPositionController::class);

        // Vehicle
        Route::apiResource('vehicles', VehicleController::class);

        // Assigned Vehicles
        Route::apiResource('assigned-vehicles', AssignedVehicleController::class);

        // Supplier
        Route::apiResource('suppliers', SupplierController::class);

        // Ore
        Route::apiResource('ores', OreController::class);

        // Dispatches
        Route::apiResource('dispatches', DispatchController::class);
        Route::post('dispatches/seek-driver-vehicle', [DispatchController::class, 'seekDriverVehicle']);

        // Cost Prices
        Route::apiResource('cost-prices', CostPriceController::class);

        // Trips
        Route::apiResource('trips', TripController::class);

        // Consolidated data endpoint
        Route::get('/consolidated-data', [ConsolidatedDataController::class, 'getConsolidatedData']);
    });
});


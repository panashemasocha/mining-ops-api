<?php

use App\Http\Controllers\V1\AccountingController;
use App\Http\Controllers\V1\AssignedVehicleController;
use App\Http\Controllers\V1\ConsolidatedDataController;
use App\Http\Controllers\V1\CostPriceController;
use App\Http\Controllers\V1\DieselAllocationController;
use App\Http\Controllers\V1\DieselAllocationTypeController;
use App\Http\Controllers\V1\DispatchController;
use App\Http\Controllers\V1\ExcavatorUsageController;
use App\Http\Controllers\V1\ExpenseController;
use App\Http\Controllers\V1\JobPositionController;
use App\Http\Controllers\V1\DriverInfoController;
use App\Http\Controllers\V1\MiningSiteController;
use App\Http\Controllers\V1\OdometerReadingController;
use App\Http\Controllers\V1\OreController;
use App\Http\Controllers\V1\OreQualityGradeController;
use App\Http\Controllers\V1\OreQualityTypeController;
use App\Http\Controllers\V1\OreTypeController;
use App\Http\Controllers\V1\PaymentMethodController;
use App\Http\Controllers\V1\SupplierController;
use App\Http\Controllers\V1\TripController;
use App\Http\Controllers\V1\VehicleCategoryController;
use App\Http\Controllers\V1\VehicleController;
use App\Http\Controllers\V1\VehicleSubTypeController;
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

        // Vehicle
        Route::apiResource('vehicles-category', VehicleCategoryController::class);

        // Vehicle
        Route::apiResource('vehicles-sub type', VehicleSubTypeController::class);

        // Diesel Allocation types
        Route::apiResource('diesel-allocation-types', DieselAllocationTypeController::class);

        //Diesel Allocation
        Route::apiResource('diesel-allocation', DieselAllocationController::class);

        // Assigned Vehicles
        Route::apiResource('assigned-vehicles', AssignedVehicleController::class);


        //Excavator
        Route::apiResource('excavator-usages', ExcavatorUsageController::class);

        //Odometer Readings
        Route::apiResource('odometer-readings',OdometerReadingController::class);

         // Trips
         Route::apiResource('trips', TripController::class);

         //Bulk Trips
         Route::post('/trips/bulk', [TripController::class, 'bulkStore']);

        // Supplier
        Route::apiResource('suppliers', SupplierController::class);

        // Dispatches
        Route::apiResource('dispatches', DispatchController::class);
        Route::post('dispatches/seek-driver-vehicle', [DispatchController::class, 'seekDriverVehicle']);

        // Cost Prices
        Route::apiResource('cost-prices', CostPriceController::class);

        //Payment Method
        Route::apiResource('payment-methods', PaymentMethodController::class);

        // Ores
        Route::prefix('ores')->group(function () {
            //Ore Parent directory
            Route::apiResource('/', OreController::class);

            //Quantities
            Route::get('quantities', [OreController::class, 'quantities']);

            //Ore Type
            Route::apiResource('type', OreTypeController::class);

            Route::prefix('quality')->group(function () {
                //Ore Quality Type
                Route::apiResource('type', OreQualityTypeController::class);
                //Ore Quality Grade
                Route::apiResource('grade', OreQualityGradeController::class);
            });
        });

        //Mining Site
        Route::apiResource('mining-sites', MiningSiteController::class);

       

        // Consolidated data endpoint
        Route::post('/consolidated-data', [ConsolidatedDataController::class, 'getConsolidatedData']);

        //Expenses
        Route::get('expenses', [ExpenseController::class, 'index']);
        Route::get('expenses/{id}', [ExpenseController::class, 'show']);
        Route::post('expenses', [ExpenseController::class, 'store']);

        // Accounting  
        Route::get('cashbook', [AccountingController::class, 'cashbook']);

    });
});


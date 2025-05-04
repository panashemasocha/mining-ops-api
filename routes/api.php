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
use App\Http\Controllers\V1\FleetStatisticalDataController;
use App\Http\Controllers\V1\FundingRequestController;
use App\Http\Controllers\V1\JobPositionController;
use App\Http\Controllers\V1\DriverInfoController;
use App\Http\Controllers\V1\MiningSiteController;
use App\Http\Controllers\V1\OdometerReadingController;
use App\Http\Controllers\V1\OreController;
use App\Http\Controllers\V1\OreLoaderController;
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
        Route::prefix('users')->group(function () {
            //Index
            Route::apiResource('/', UserController::class);
            Route::apiResource('departments', DepartmentController::class);
            Route::apiResource('branches', BranchController::class);
            Route::apiResource('roles', UserRoleController::class);
            Route::apiResource('driver-info', DriverInfoController::class);
            Route::apiResource('job-positions', JobPositionController::class);
        });

        Route::post('fleet-stats', [FleetStatisticalDataController::class, 'getFleetStatistics']);

        // Vehicles
        Route::prefix('vehicles')->group(function () {
            // Index
            Route::apiResource('/', VehicleController::class);
            // Vehicle Category
            Route::apiResource('categories', VehicleCategoryController::class);
            // Vehicle Sub Category
            Route::apiResource('sub-types', VehicleSubTypeController::class);

            //Excavator
            Route::apiResource('excavator-usages', ExcavatorUsageController::class);

            //Odometer Readings
            Route::apiResource('odometer-readings', OdometerReadingController::class);

            // Assigned Drivers
            Route::apiResource('assigned-drivers', AssignedVehicleController::class);

            //Diesel Allocations
            Route::prefix('diesel-allocations')->group(function () {

                //Index
                Route::apiResource('/', DieselAllocationController::class);
                //types
                Route::apiResource('types', DieselAllocationTypeController::class);

                //Bulk Insert
                Route::post('bulk-store', [DieselAllocationController::class, 'bulkStore']);

            });
        });

        // Ores
        Route::prefix('ores')->group(function () {
            //Index 
            Route::apiResource('/', OreController::class);

            //Ore Loaders
            Route::apiResource('loaders', OreLoaderController::class);

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

        //Accounting
        Route::prefix('accounting')->group(function () {
           
             // Accounts  
             Route::get('accounts', [AccountingController::class, 'accountsWithBalances']);

            // cashbook  
            Route::get('cashbook', [AccountingController::class, 'cashbook']);

            //Expenses
            Route::prefix('expenses')->group(function () {
                Route::get('/', [ExpenseController::class, 'index']);
                Route::get('/{id}', [ExpenseController::class, 'show']);
                Route::post('/', [ExpenseController::class, 'store']);
            });

            //Request Funding
            Route::apiResource('funding-request', FundingRequestController::class);

            // Cost Prices
            Route::apiResource('cost-prices', CostPriceController::class);

            //Payment Method
            Route::apiResource('payment-methods', PaymentMethodController::class);

        });

        Route::prefix('trips')->group(function () {
            // Trips
            Route::apiResource('/', TripController::class);

            //Bulk Insert
            Route::post('bulk-store', [TripController::class, 'bulkStore']);
        });

        // Dispatches
        Route::prefix('dispatches')->group(function () {
            Route::apiResource('/', DispatchController::class);
            Route::post('seek-driver-vehicle', [DispatchController::class, 'seekDriverVehicle']);
            Route::post('bulk-store', [DispatchController::class, 'storeWithTripsAndDieselAllocations']);
        });

        //Mining Site
        Route::apiResource('mining-sites', MiningSiteController::class);

        // Consolidated data
        Route::post('/consolidated-data', [ConsolidatedDataController::class, 'getConsolidatedData']);

        // Supplier
        Route::apiResource('suppliers', SupplierController::class);



    });
});


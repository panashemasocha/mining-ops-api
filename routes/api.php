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
use App\Http\Controllers\V1\RequisitionController;
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

        //Users
        Route::apiResource('users', UserController::class);

        //Users Routes
        Route::prefix('users')->group(function () {
            Route::apiResource('departments', DepartmentController::class);
            Route::apiResource('branches', BranchController::class);
            Route::apiResource('roles', UserRoleController::class);
            Route::apiResource('driver-info', DriverInfoController::class);
        });
            Route::apiResource('/users/job-positions', JobPositionController::class);

        Route::post('fleet-stats', [FleetStatisticalDataController::class, 'getFleetStatistics']);

        Route::apiResource('vehicles', VehicleController::class);

        // Vehicles
        Route::prefix('vehicles')->group(function () {

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

            Route::apiResource('diesel-allocations', DieselAllocationController::class);

            //Diesel Allocations
            Route::prefix('diesel-allocations')->group(function () {
                //types
                Route::apiResource('types', DieselAllocationTypeController::class);

                //Bulk Insert
                Route::post('bulk-store', [DieselAllocationController::class, 'bulkStore']);

            });
        });

        Route::apiResource('ores', OreController::class);

        //Ore Type
        Route::apiResource('ores/type', OreTypeController::class);
        // Ores
        Route::prefix('ores')->group(function () {
         
            //Quantities
            Route::get('quantities', [OreController::class, 'quantities']);

            //Ore Quality Type
            Route::apiResource('quality-types', OreQualityTypeController::class);
            //Ore Quality Grade
            Route::apiResource('quality-grade', OreQualityGradeController::class);

        });

        //Accounting
        Route::prefix('accounting')->group(function () {

            // Accounts  
            Route::prefix('accounts')->group(function () {
                //Index
                Route::get('/', [AccountingController::class, 'accountsWithBalances']);
                Route::get('search', [AccountingController::class, 'searchAccountTransactions']);
                Route::get('/{id}', [AccountingController::class, 'accountTransactions']);
            });

            // invoices  
            Route::post('invoices', [AccountingController::class, 'invoiceReport']);

            // invoices  
            Route::post('payments', [AccountingController::class, 'paymentReport']);
            Route::post('receipt', [AccountingController::class, 'storeReceipt']);

            // cashbook  
            Route::get('cashbook', [AccountingController::class, 'cashbook']);

            //Expenses
            Route::prefix('expenses')->group(function () {
                Route::get('/', [ExpenseController::class, 'index']);
                Route::get('/{id}', [ExpenseController::class, 'show']);
                Route::post('/', [ExpenseController::class, 'store']);
            });

            //Requisitions
            Route::apiResource('requisitions', RequisitionController::class);

            // Cost Prices
            Route::apiResource('cost-prices', CostPriceController::class);

            //Payment Method
            Route::apiResource('payment-methods', PaymentMethodController::class);

        });

        // Trips
        Route::apiResource('trips', TripController::class);
        Route::prefix('trips')->group(function () {

            //Bulk Insert
            Route::post('bulk-store', [TripController::class, 'bulkStore']);
        });

        Route::apiResource('dispatches', DispatchController::class);

        // Dispatches
        Route::prefix('dispatches')->group(function () {
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


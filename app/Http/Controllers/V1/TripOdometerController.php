<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreOdometerReadingRequest;
use App\Http\Requests\UpdateOdometerReadingRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\OdometerReadingResource;
use App\Http\Resources\TripResource;
use App\Models\Account;
use App\Models\CostPrice;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use App\Models\OdometerReading;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TripOdometerController extends Controller
{
    /**
     * Update trip and handle odometer reading based on status changes
     *
     * @param UpdateTripRequest $tripRequest
     * @param StoreOdometerReadingRequest $storeOdometerRequest
     * @param UpdateOdometerReadingRequest
     *  $updateOdometerRequest
     * @param int $tripId
     * @return array
     */
    public function updateTripAndOdometer(
        UpdateTripRequest $tripRequest,
        StoreOdometerReadingRequest $storeOdometerRequest,
        UpdateOdometerReadingRequest $updateOdometerRequest,
        int $tripId
    ) {
        return DB::transaction(function () use ($tripRequest, $storeOdometerRequest, $updateOdometerRequest, $tripId) {
            // Find and update the trip
            $trip = Trip::findOrFail($tripId);
            $oldStatus = $trip->status;
            $trip->update($tripRequest->validated());

            // Initialize variables for response
            $odometerReading = null;

            // Handle different status transition scenarios
            if ($oldStatus === 'pending' && $trip->status === 'in-transit') {
                // Create new odometer reading when moving from pending to in-transit
                $storeData = $storeOdometerRequest->validated();
                $storeData['trip_id'] = $tripId;
                $storeData['vehicle_id'] = $trip->vehicle_id;
                $odometerReading = OdometerReading::create($storeData);
            } elseif ($oldStatus === 'in-transit' && $trip->status === 'fulfilled') {
                // Update existing odometer reading when moving from in-transit to fulfilled
                $existingReading = OdometerReading::where('trip_id', $tripId)->first();

                if ($existingReading) {
                    $updateData = $updateOdometerRequest->validated();
                    $existingReading->update($updateData);
                    $odometerReading = $existingReading;
                }

                // post mining expenses
                $this->postMiningExpenses($trip, $trip->dieselAllocation);
            }

            $response = [
                'trip' => new TripResource($trip),
            ];

            // Add odometer reading to response if it exists
            if ($odometerReading) {
                $response['odometerReading'] =
                    new OdometerReadingResource(
                        $odometerReading->load('vehicle', 'trip')
                    );
            }
            return $response;
        });
    }

    protected function postMiningExpenses(Trip $trip, $dieselAllocation = null)
    {
        $dispatch = $trip->dispatch;
        // Validate supplier exists
        if (!$dispatch->ore->supplier) {
            throw new \Exception("Supplier not found for dispatch #{$dispatch->id}");
        }
        $supplier = $dispatch->ore->supplier;
        $supplierName = "{$supplier->first_name} {$supplier->last_name}";
        $date = Carbon::now()->toDateString();

        // Expense accounts
        $oreExpense = Account::findOrFail(4);
        $loadExpense = Account::findOrFail(6);
        $dieselExpense = Account::findOrFail(5);
        // Liability account: Accounts Payable
        $apAccount = Account::where('id', 7)->firstOrFail();

        // Helper to create an invoice
        $createInvoice = function (string $description, int $expAcctId, float $amt) use ($supplier, $trip, $date, $apAccount) {

            $txData = [
                'trans_date' => $date,
                'trip_id' => $trip->id,
                'trans_type' => 'invoice',
                'description' => $description,
                'created_by' => auth()->id(),
            ];

            if ($expAcctId === 4) {
                $txData['supplier_id'] = $supplier->id;
            }

            // Create the transaction
            $tx = GLTransaction::create($txData);


            // Debit expense
            GLEntry::create([
                'trans_id' => $tx->id,
                'account_id' => $expAcctId,
                'debit_amt' => $amt,
                'credit_amt' => 0,
            ]);

            // Credit Accounts Payable
            GLEntry::create([
                'trans_id' => $tx->id,
                'account_id' => $apAccount->id,
                'debit_amt' => 0,
                'credit_amt' => $amt,
            ]);
        };

        // 1) Ore cost
        $oreQty = $trip->ore_quantity;
        $oreCostAmt = $dispatch->ore_cost_per_tonne * $oreQty;
        $createInvoice(
            "Purchase Invoice – Ore Cost ({$dispatch->ore->oreType->type}) for Trip #{$trip->id}",
            $oreExpense->id,
            $oreCostAmt
        );

        // 2) Loading cost
        if ($dispatch->loading_method === 'manual') {
            $loadCostAmt = $dispatch->loading_cost_per_tonne * $oreQty;
            $createInvoice(
                "Purchase Invoice – Ore Loading Services for Trip #{$trip->id}",
                $loadExpense->id,
                $loadCostAmt
            );
        }

        // 3) Diesel cost
        if ($dieselAllocation) {
            $dieselAllocation->load('vehicle');
            if (!$dieselAllocation->vehicle) {
                throw new \Exception("Vehicle not found for diesel allocation #{$dieselAllocation->id}");
            }
            $dieselPrice = CostPrice::where('commodity', 'diesel cost')
                ->latest('created_at')
                ->firstOrFail();
            $cost = $dieselAllocation->litres * $dieselPrice->price;

            $createInvoice(
                "Purchase Invoice – Diesel Allocation ({$dieselAllocation->litres} L – {$dieselAllocation->vehicle->reg_number})",
                $dieselExpense->id,
                $cost,

            );
        }
    }

}
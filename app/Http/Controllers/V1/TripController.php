<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\BulkStoreTripRequest;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Account;
use App\Models\CostPrice;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $trips = $request->query('paging', 'true') === 'false'
            ? Trip::all()
            : Trip::paginate(10);
        return TripResource::collection($trips);
    }

    public function store(StoreTripRequest $request)
    {
        $trip = Trip::create($request->validated());
        return new TripResource($trip);
    }

    public function show($id)
    {
        $trip = Trip::findOrFail($id);
        return new TripResource($trip);
    }

    public function update(UpdateTripRequest $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $oldStatus = $trip->status;
        $trip->update($request->validated());

        // If just transitioned to 'fulfilled', post expenses
        if ($oldStatus !== 'fulfilled' && $trip->status === 'fulfilled') {

            $this->postMiningExpenses($trip, $trip->dieselAllocation);
        }
        return new TripResource($trip);
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
            $tx = GLTransaction::create([
                'trans_date' => $date,
                'supplier_id' => $supplier->id,
                'trip_id' => $trip->id,
                'trans_type' => 'invoice',
                'description' => $description,
                'created_by' => auth()->id(),
            ]);

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



    public function destroy($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();
        return response()->json(['message' => 'Trip deleted'], 200);
    }

    public function bulkStore(BulkStoreTripRequest $request)
    {
        $tripsData = $request->validated()['trips'];

        $trips = collect($tripsData)->map(function ($tripData) {
            return Trip::create($tripData);
        });

        return TripResource::collection($trips);
    }
}
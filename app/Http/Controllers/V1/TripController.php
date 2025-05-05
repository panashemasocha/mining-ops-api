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
        $paymentMethod = $dispatch->ore->supplier->payment_method_id ?? 1;
        $supplierName = $dispatch->ore->supplier->first_name . ' ' . $dispatch->ore->supplier->last_name;
        $date = Carbon::now()->toDateString();
        $oreQty = $trip->ore_quantity;
        $oreCostAmt = $dispatch->ore_cost_per_tonne * $oreQty;
        $loadCostAmt = $dispatch->loading_cost_per_tonne * $oreQty;

        $asset = Account::where('id', $paymentMethod)->firstOrFail();
        $oreExpense = Account::where('id', 4)->firstOrFail();
        $oreLoadingExpense = Account::where('id', 6)->firstOrFail();

        // 1) Ore Cost Transaction
        $txOre = GLTransaction::create([
            'trans_date' => $date,
            'supplier_id' => $dispatch->ore->supplier->id,
            'trip_id' => $trip->id,
            'trans_type'=> 'invoice',
            'description' => "Ore ({$dispatch->ore->oreType->type}) Cost -{$supplierName}-{$dispatch->id}",
            'created_by' => auth()->id(),
        ]);

        GLEntry::create([
            'trans_id' => $txOre->id,
            'account_id' => $oreExpense->id,
            'debit_amt' => $oreCostAmt,
            'credit_amt' => 0,
        ]);
        GLEntry::create([
            'trans_id' => $txOre->id,
            'account_id' => $asset->id,
            'debit_amt' => 0,
            'credit_amt' => $oreCostAmt,
        ]);

        // 2) Loading Cost (if manual)
        if ($dispatch->loading_method === "manual") {
            $txLoading = GLTransaction::create([
                'trans_date' => $date,
                'supplier_id' => $dispatch->ore->supplier->id,
                'trip_id' => $trip->id,
                'trans_type'=> 'invoice',
                'description' => "Loading cost-{$supplierName}-{$dispatch->id}",
                'created_by' => auth()->id(),
            ]);

            GLEntry::create([
                'trans_id' => $txLoading->id,
                'account_id' => $oreLoadingExpense->id,
                'debit_amt' => $loadCostAmt,
                'credit_amt' => 0,
            ]);
            GLEntry::create([
                'trans_id' => $txLoading->id,
                'account_id' => $asset->id,
                'debit_amt' => 0,
                'credit_amt' => $loadCostAmt,
            ]);
        }

        // 3) Diesel Allocation (if provided)
        if ($dieselAllocation != null) {
            $dieselExpense = Account::where('id', 5)->firstOrFail();
            $dieselPrice = CostPrice::where('commodity', 'diesel cost')
                ->latest('created_at')
                ->firstOrFail();

            // Validate allocation instance
            if (!$dieselAllocation->id) {
                throw new \Exception("Invalid diesel allocation data");
            }

            // Load vehicle relationship
            $dieselAllocation->load('vehicle');
            if (!$dieselAllocation->vehicle) {
                throw new \Exception("Vehicle not found for diesel allocation #{$dieselAllocation->id}");
            }

            $cost = $dieselAllocation->litres * $dieselPrice->price;

            $txDiesel = GLTransaction::create([
                'trans_date' => $date,
                'supplier_id' => $dispatch->ore->supplier->id,
                'trip_id' => $trip->id,
                'trans_type'=> 'invoice',
                'description' => "Diesel cost - {$dieselAllocation->vehicle->reg_number} - {$dieselAllocation->litres}L",
                'created_by' => auth()->id(),
            ]);

            GLEntry::create([
                'trans_id' => $txDiesel->id,
                'account_id' => $dieselExpense->id,
                'debit_amt' => $cost,
                'credit_amt' => 0,
            ]);

            GLEntry::create([
                'trans_id' => $txDiesel->id,
                'account_id' => $asset->id,
                'debit_amt' => 0,
                'credit_amt' => $cost,
            ]);
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
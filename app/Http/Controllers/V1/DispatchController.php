<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreDispatchRequest;
use App\Http\Requests\UpdateDispatchRequest;
use App\Http\Resources\DispatchResource;
use App\Models\Account;
use App\Models\Dispatch;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use App\Models\Ore;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $dispatches = $request->query('paging', 'true') === 'false' 
            ? Dispatch::all() 
            : Dispatch::paginate(10);
        return DispatchResource::collection($dispatches);
    }

    public function store(StoreDispatchRequest $request)
    {
        $dispatch = Dispatch::create($request->validated());
        return new DispatchResource($dispatch);
    }

    public function show($id)
    {
        $dispatch = Dispatch::findOrFail($id);
        return new DispatchResource($dispatch);
    }

    public function update(UpdateDispatchRequest $request, $id)
    {
        $dispatch = Dispatch::findOrFail($id);
        $oldStatus = $dispatch->status;

        $dispatch->update($request->validated());

        // If just transitioned to 'accepted', post expenses
        if ($oldStatus !== 'accepted' && $dispatch->status === 'accepted') {
            $this->postMiningExpenses($dispatch, $request->input('payment_method'));
        }

        return new DispatchResource($dispatch);
    }

    protected function postMiningExpenses(Dispatch $dispatch, string $paymentMethod)
    {
        $supplierName = $dispatch->supplier->first_name . ' ' . $dispatch->supplier->last_name;
        $date         = Carbon::now()->toDateString();
        $oreQty       = $dispatch->ore_quantity;
        $oreCostAmt   = $dispatch->ore_cost_per_tonne * $oreQty;
        $loadCostAmt  = $dispatch->loading_cost_per_tonne * $oreQty;

        // Map user‐supplied payment_method to the right asset account
        $assetAccountName = match($paymentMethod) {
            'Cash'          => 'Cash on Hand',
            'Bank Transfer' => 'Bank',
            'Ecocash'       => 'Ecocash',
            default         => 'Cash on Hand',
        };

        $asset   = Account::where('account_name', $assetAccountName)->firstOrFail();
        $expense = Account::where('account_name', 'Mining expenses')->firstOrFail();

        // 1) Ore Cost
        $tx1 = GLTransaction::create([
            'trans_date'  => $date,
            'description' => "Ore ({$dispatch->ore->type}) Cost -{$supplierName}-{$dispatch->id}",
            'created_by'  => auth()->id(),
        ]);
        GLEntry::create([
            'trans_id'   => $tx1->id,
            'account_id' => $expense->id,
            'debit_amt'  => $oreCostAmt,
            'credit_amt' => 0,
        ]);
        GLEntry::create([
            'trans_id'   => $tx1->id,
            'account_id' => $asset->id,
            'debit_amt'  => 0,
            'credit_amt' => $oreCostAmt,
        ]);

        // 2) Loading Cost
        $tx2 = GLTransaction::create([
            'trans_date'  => $date,
            'description' => "Loading cost-{$supplierName}-{$dispatch->id}",
            'created_by'  => auth()->id(),
        ]);
        GLEntry::create([
            'trans_id'   => $tx2->id,
            'account_id' => $expense->id,
            'debit_amt'  => $loadCostAmt,
            'credit_amt' => 0,
        ]);
        GLEntry::create([
            'trans_id'   => $tx2->id,
            'account_id' => $asset->id,
            'debit_amt'  => 0,
            'credit_amt' => $loadCostAmt,
        ]);
    }

    public function destroy($id)
    {
        $dispatch = Dispatch::findOrFail($id);
        $dispatch->delete();
        return response()->json(['message' => 'Dispatch deleted'], 200);
    }

    public function seekDriverVehicle(Request $request)
    {
        $request->validate([
            'ore_id' => 'required|exists:ores,id',
        ]);

        $ore = Ore::findOrFail($request->ore_id);
        $oreLocation = [$ore->latitude, $ore->longitude];

        // Find available drivers (assumes User model has a 'role' or similar relation)
        $drivers = User::whereHas('role', function ($query) {
            $query->where('name', 'driver');
        })->get();

        $vehicles = Vehicle::where('status', 'off trip')->get();

        $results = [];
        foreach ($drivers as $driver) {
            // Assuming drivers have location data in a related table or attributes
            $driverLocation = [$driver->latitude ?? 0, $driver->longitude ?? 0];
            $driverDistance = $this->calculateDistance($oreLocation, $driverLocation);

            foreach ($vehicles as $vehicle) {
                $vehicleLocation = [$vehicle->last_known_latitude, $vehicle->last_known_longitude];
                $vehicleDistance = $this->calculateDistance($oreLocation, $vehicleLocation);

                $results[] = [
                    'driver' => $driver,
                    'vehicle' => $vehicle,
                    'driver_distance' => $driverDistance,
                    'vehicle_distance' => $vehicleDistance,
                ];
            }
        }

        // Sort by driver distance
        usort($results, fn($a, $b) => $a['driver_distance'] <=> $b['driver_distance']);

        return response()->json($results);
    }

    private function calculateDistance($point1, $point2)
    {
        // Simple Euclidean distance (replace with Haversine formula for real-world use)
        $latDiff = $point1[0] - $point2[0];
        $lonDiff = $point1[1] - $point2[1];
        return sqrt($latDiff * $latDiff + $lonDiff * $lonDiff);
    }
}
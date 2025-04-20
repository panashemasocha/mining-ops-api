<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreDispatchRequest;
use App\Http\Requests\UpdateDispatchRequest;
use App\Http\Resources\DispatchResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VehicleResource;
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
        $date = Carbon::now()->toDateString();
        $oreQty = $dispatch->ore_quantity;
        $oreCostAmt = $dispatch->ore_cost_per_tonne * $oreQty;
        $loadCostAmt = $dispatch->loading_cost_per_tonne * $oreQty;

        // Map user‐supplied payment_method to the right asset account
        $assetAccountName = match ($paymentMethod) {
            'Cash' => 'Cash on Hand',
            'Bank Transfer' => 'Bank',
            'Ecocash' => 'Ecocash',
            default => 'Cash on Hand',
        };

        $asset = Account::where('account_name', $assetAccountName)->firstOrFail();
        $expense = Account::where('account_name', 'Mining expenses')->firstOrFail();

        // 1) Ore Cost
        $tx1 = GLTransaction::create([
            'trans_date' => $date,
            'description' => "Ore ({$dispatch->ore->type}) Cost -{$supplierName}-{$dispatch->id}",
            'created_by' => auth()->id(),
        ]);
        GLEntry::create([
            'trans_id' => $tx1->id,
            'account_id' => $expense->id,
            'debit_amt' => $oreCostAmt,
            'credit_amt' => 0,
        ]);
        GLEntry::create([
            'trans_id' => $tx1->id,
            'account_id' => $asset->id,
            'debit_amt' => 0,
            'credit_amt' => $oreCostAmt,
        ]);

        // 2) Loading Cost
        $tx2 = GLTransaction::create([
            'trans_date' => $date,
            'description' => "Loading cost-{$supplierName}-{$dispatch->id}",
            'created_by' => auth()->id(),
        ]);
        GLEntry::create([
            'trans_id' => $tx2->id,
            'account_id' => $expense->id,
            'debit_amt' => $loadCostAmt,
            'credit_amt' => 0,
        ]);
        GLEntry::create([
            'trans_id' => $tx2->id,
            'account_id' => $asset->id,
            'debit_amt' => 0,
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

        // Fetch available drivers
        $drivers = User::where('job_position_id', 5)
            ->where('status', 1)
            ->with('driverInfo')
            ->get();
        $driverResources = UserResource::collection($drivers)->toArray(request());

        // Fetch available vehicles
        $vehicles = Vehicle::where('status', 'off trip')->get();
        $vehicleResources = VehicleResource::collection($vehicles)->toArray(request());

        $results = [];

        foreach ($driverResources as $driver) {
           // driver coordinates
            $driverLat = $driver['driverInfo']['lastKnownLocation']['latitude'] ?? null;
            $driverLon = $driver['driverInfo']['lastKnownLocation']['longitude'] ?? null;

            foreach ($vehicleResources as $vehicle) {
                //  vehicle and ore coordinates
                $vehicleLat = $vehicle['lastKnownLocation']['latitude'] ?? null;
                $vehicleLon = $vehicle['lastKnownLocation']['longitude'] ?? null;

                // Calculate distances
                $driverToVehicleDistance = $this->calculateHaversineDistance(
                    $driverLat,
                    $driverLon,
                    $vehicleLat,
                    $vehicleLon
                );

                $vehicleToOreDistance = $this->calculateHaversineDistance(
                    $oreLocation[0],
                    $oreLocation[1],
                    $vehicleLat,
                    $vehicleLon
                );

                $results[] = [
                    'driver' => $driver,
                    'vehicle' => $vehicle,
                    'driverToVehicleDistance' => round($driverToVehicleDistance, 2),
                    'vehicleToOreDistance' => round($vehicleToOreDistance, 2),
                    'oreLocation' => [
                        'latitude' => $oreLocation[0],
                        'longitude' => $oreLocation[1]
                    ]
                ];
            }
        }

        // Sort by vehicle-ore proximity
        usort($results, fn($a, $b) => $a['vehicleToOreDistance'] <=> $b['vehicleToOreDistance']);

        return response()->json($results);
    }

    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2)
            return null;

        $earthRadius = 6371; // Kilometers

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) *
            pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
}
<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\SeekDriverVehicleRequest;
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

    public function seekDriverVehicle(SeekDriverVehicleRequest $request)
    {
        $request->validate([
            'ore_id' => 'required|exists:ores,id',
        ]);
    
        $ore = Ore::findOrFail($request->ore_id);
        $oreLat = $ore->latitude;
        $oreLon = $ore->longitude;
    
        // 1) Only grab drivers who actually have non‑null coords
        $drivers = User::where('job_position_id', 5)
            ->where('status', 1)
            ->whereHas('driverInfo', function($q) {
                $q->whereNotNull('last_known_latitude')
                  ->whereNotNull('last_known_longitude');
            })
            ->with('driverInfo') 
            ->get();
    
        $vehicles = Vehicle::where('status', 'off trip')
            // likewise ensure vehicle coords exist
            ->whereNotNull('last_known_latitude')
            ->whereNotNull('last_known_longitude')
            ->get();
    
        $results = [];
    
        foreach ($drivers as $driver) {
            $driverLat = $driver->driverInfo->last_known_latitude;
            $driverLon = $driver->driverInfo->last_known_longitude;
    
            foreach ($vehicles as $vehicle) {
                $vehicleLat = $vehicle->last_known_latitude;
                $vehicleLon = $vehicle->last_known_longitude;
    
                $driverToVehicleDistance = $this->calculateHaversineDistance(
                    $driverLat, $driverLon,
                    $vehicleLat, $vehicleLon
                );
    
                $vehicleToOreDistance = $this->calculateHaversineDistance(
                    $oreLat, $oreLon,
                    $vehicleLat, $vehicleLon
                );
    
                $results[] = [
                    'driver'                   => new UserResource($driver),
                    'vehicle'                  => new VehicleResource($vehicle),
                    'driverToVehicleDistance'  => round($driverToVehicleDistance, 2),
                    'vehicleToOreDistance'     => round($vehicleToOreDistance, 2),
                    'oreLocation'              => [
                        'latitude'  => $oreLat,
                        'longitude' => $oreLon,
                    ],
                ];
            }
        }
    
        // Sort by proximity of vehicle → ore
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
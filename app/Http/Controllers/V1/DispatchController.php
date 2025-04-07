<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreDispatchRequest;
use App\Http\Requests\UpdateDispatchRequest;
use App\Http\Resources\DispatchResource;
use App\Models\Dispatch;
use App\Models\Ore;
use App\Models\User;
use App\Models\Vehicle;
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
        $dispatch->update($request->validated());
        return new DispatchResource($dispatch);
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

        $results = ['drivers'=>$drivers,
                     'vehicle'=> $vehicles];
        foreach ($drivers as $driver) {
            // Assuming drivers have location data in a related table or attributes
            $driverLocation = [$driver->latitude ?? 0, $driver->longitude ?? 0];
            $driverDistance = $this->calculateDistance($oreLocation, $driverLocation);

            foreach ($vehicles as $vehicle) {
                $vehicleLocation = [$vehicle->last_known_latitude, $vehicle->last_known_longitude];
                $vehicleDistance = $this->calculateDistance($oreLocation, $vehicleLocation);

                $results[] = [
                    'driver_id' => $driver->id,
                    'vehicle_id' => $vehicle->id,
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
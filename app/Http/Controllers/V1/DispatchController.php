<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\SeekDriverVehicleRequest;
use App\Http\Requests\StoreDispatchRequest;
use App\Http\Requests\UpdateDispatchRequest;
use App\Http\Resources\DieselAllocationResource;
use App\Http\Resources\DispatchResource;
use App\Http\Resources\TripResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VehicleResource;
use App\Models\Account;
use App\Models\CostPrice;
use App\Models\Dispatch;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use App\Models\Ore;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreDispatchWithTripsAndAllocationsRequest;
use App\Models\Trip;
use App\Models\DieselAllocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function storeWithTripsAndDieselAllocations(StoreDispatchWithTripsAndAllocationsRequest $request)
    {
        DB::beginTransaction();

        try {
            // 1. Create Dispatch
            $dispatch = Dispatch::create($request->input('dispatch'));

            // 2. Create Diesel Allocations First
            $dieselAllocations = collect();
            $vehicleAllocationMap = [];

            if ($request->has('dieselAllocations')) {
                foreach ($request->input('dieselAllocations') as $allocationData) {
                    $allocation = DieselAllocation::create($allocationData);
                    if (!$allocation->id) {
                        throw new \Exception("Failed to create diesel allocation for vehicle {$allocationData['vehicle_id']}");
                    }
                    $dieselAllocations->push($allocation);
                    $vehicleAllocationMap[$allocation->vehicle_id] = $allocation->id;
                }
            }

            // 3. Create Trips with Allocation Mapping
            $trips = collect();
            foreach ($request->input('trips') as $tripData) {
                $tripData['dispatch_id'] = $dispatch->id;

                // Auto-link diesel allocation if exists
                if (!empty($vehicleAllocationMap)) {
                    $vehicleId = $tripData['vehicle_id'];
                    if (isset($vehicleAllocationMap[$vehicleId])) {
                        $tripData['diesel_allocation_id'] = $vehicleAllocationMap[$vehicleId];
                    }
                }

                $trip = Trip::create($tripData);
                $trips->push($trip);
            }

            DB::commit();

            return response()->json([
                'dispatch' => new DispatchResource($dispatch),
                'trips' => TripResource::collection($trips),
                'dieselAllocations' => $dieselAllocations->isNotEmpty()
                    ? DieselAllocationResource::collection($dieselAllocations)
                    : [],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
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


    public function seekDriverVehicle(SeekDriverVehicleRequest $request)
    {
        $ore = Ore::findOrFail($request->ore_id);
        $oreLat = $ore->latitude;
        $oreLon = $ore->longitude;

        $drivers = User::where('job_position_id', 5)
            ->where('status', 1)
            ->whereHas(
                'driverInfo',
                fn($q) => $q
                    ->whereNotNull('last_known_latitude')
                    ->whereNotNull('last_known_longitude')
            )
            ->with('driverInfo')
            ->get();

        $vehicles = Vehicle::with('vehicleSubType')
            ->where('status', 'off trip')
            ->where('sub_type_id', $request->sub_type_id)
            ->whereNotNull('last_known_latitude')
            ->whereNotNull('last_known_longitude')
            ->get();

        $results = [];
        foreach ($drivers as $driver) {
            $dLat = $driver->driverInfo->last_known_latitude;
            $dLon = $driver->driverInfo->last_known_longitude;

            foreach ($vehicles as $vehicle) {
                $vLat = $vehicle->last_known_latitude;
                $vLon = $vehicle->last_known_longitude;

                $results[] = [
                    'driver' => new UserResource($driver),
                    'vehicle' => new VehicleResource($vehicle),
                    'driverToVehicleDistance' => round(
                        $this->calculateHaversineDistance($dLat, $dLon, $vLat, $vLon),
                        2
                    ),
                    'vehicleToOreDistance' => round(
                        $this->calculateHaversineDistance($oreLat, $oreLon, $vLat, $vLon),
                        2
                    ),
                ];
            }
        }

        // sort by vehicle → ore
        usort($results, fn($a, $b) => $a['vehicleToOreDistance'] <=> $b['vehicleToOreDistance']);

        // pagination
        $page = (int) $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($results, $offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $slice,
            count($results),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return response()->json([
            'data' => $paginator->items(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ]);
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
<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = $request->query('paging', 'true') === 'false'
            ? Vehicle::all()
            : Vehicle::paginate(10);
        return VehicleResource::collection($vehicles);
    }

    public function store(StoreVehicleRequest $request)
    {
        $vehicle = Vehicle::create($request->validated());
        return new VehicleResource($vehicle);
    }

    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return new VehicleResource($vehicle);
    }

    public function update(UpdateVehicleRequest $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($request->validated());
        return new VehicleResource($vehicle);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted'], 200);
    }
}
<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreAssignedVehicleRequest;
use App\Http\Requests\UpdateAssignedVehicleRequest;
use App\Http\Resources\AssignedVehicleResource;
use App\Models\AssignedVehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssignedVehicleController extends Controller
{
    public function index(Request $request)
    {
        $assignedVehicles = $request->query('paging', 'true') === 'false' 
            ? AssignedVehicle::all() 
            : AssignedVehicle::paginate(10);
        return AssignedVehicleResource::collection($assignedVehicles);
    }

    public function store(StoreAssignedVehicleRequest $request)
    {
        $assignedVehicle = AssignedVehicle::create($request->validated());
        return new AssignedVehicleResource($assignedVehicle);
    }

    public function show($id)
    {
        $assignedVehicle = AssignedVehicle::findOrFail($id);
        return new AssignedVehicleResource($assignedVehicle);
    }

    public function update(UpdateAssignedVehicleRequest $request, $id)
    {
        $assignedVehicle = AssignedVehicle::findOrFail($id);
        $assignedVehicle->update($request->validated());
        return new AssignedVehicleResource($assignedVehicle);
    }

    public function destroy($id)
    {
        $assignedVehicle = AssignedVehicle::findOrFail($id);
        $assignedVehicle->delete();
        return response()->json(['message' => 'Assigned vehicle deleted'], 200);
    }
}
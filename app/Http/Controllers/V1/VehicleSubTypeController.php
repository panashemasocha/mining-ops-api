<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\VehicleSubTypeResource;
use App\Models\VehicleSubType;
use App\Http\Requests\StoreVehicleSubTypeRequest;
use App\Http\Requests\UpdateVehicleSubTypeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehicleSubTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $types = $request->query('paging', 'true') === 'false'
            ? VehicleSubType::all()
            : VehicleSubType::paginate(10);

        return VehicleSubTypeResource::collection($types);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehicleSubTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleSubType $vehicleSubType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleSubType $vehicleSubType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleSubTypeRequest $request, VehicleSubType $vehicleSubType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleSubType $vehicleSubType)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\OdometerReadingResource;
use App\Models\OdometerReading;
use App\Http\Requests\StoreOdometerReadingRequest;
use App\Http\Requests\UpdateOdometerReadingRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OdometerReadingController extends Controller
{
    public function index(Request $request)
    {
        $readings = OdometerReading::orderBy('created_at', 'desc')
            ->paginate(15);

        return OdometerReadingResource::collection($readings);
          
    }

    public function store(StoreOdometerReadingRequest $request)
    {
        $reading = OdometerReading::create($request->validated());
        return new OdometerReadingResource($reading->load('vehicle', 'trip'));
    }

    public function show(OdometerReading $odometerReading)
    {
        return new OdometerReadingResource($odometerReading->load('vehicle', 'trip'));
    }

    public function update(UpdateOdometerReadingRequest $request, OdometerReading $odometerReading)
    {
        $odometerReading->update($request->validated());
        return new OdometerReadingResource($odometerReading->load('vehicle', 'trip'));
    }

    public function destroy(OdometerReading $odometerReading)
    {
        $odometerReading->delete();
        return response()->noContent();
    }
}

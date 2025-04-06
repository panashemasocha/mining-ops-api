<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
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
        $trip->update($request->validated());
        return new TripResource($trip);
    }

    public function destroy($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();
        return response()->json(['message' => 'Trip deleted'], 200);
    }
}
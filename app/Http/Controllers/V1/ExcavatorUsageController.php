<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\ExcavatorUsageResource;
use App\Models\ExcavatorUsage;
use App\Http\Requests\StoreExcavatorUsageRequest;
use App\Http\Requests\UpdateExcavatorUsageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExcavatorUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usages = $request->query('paging', 'true') === 'false'
            ? ExcavatorUsage::all()
            : ExcavatorUsage::paginate(10);

        return ExcavatorUsageResource::collection($usages);
    }

    public function store(StoreExcavatorUsageRequest $request)
    {
        $usage = ExcavatorUsage::create($request->validated());
        return new ExcavatorUsageResource($usage);
    }

    public function show(ExcavatorUsage $excavatorUsage)
    {
        return new ExcavatorUsageResource($excavatorUsage);
    }

    public function update(UpdateExcavatorUsageRequest $request, ExcavatorUsage $excavatorUsage)
    {
        $excavatorUsage->update($request->validated());
        return new ExcavatorUsageResource($excavatorUsage);
    }

    public function destroy(ExcavatorUsage $excavatorUsage)
    {
        $excavatorUsage->delete();
        return response()->json(['message' => 'Excavator usage deleted'], 200);
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\BulkStoreDieselAllocationRequest;
use App\Http\Resources\DieselAllocationResource;
use App\Models\DieselAllocation;
use App\Http\Requests\StoreDieselAllocationRequest;
use App\Http\Requests\UpdateDieselAllocationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DieselAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $allocations = $request->query('paging', 'true') === 'false'
            ? DieselAllocation::all()
            : DieselAllocation::paginate(10);

        return DieselAllocationResource::collection($allocations);

    }

    public function store(StoreDieselAllocationRequest $request)
    {
        $allocation = DieselAllocation::create($request->validated());
        return new DieselAllocationResource($allocation);
    }

    public function show(DieselAllocation $dieselAllocation)
    {
        return new DieselAllocationResource($dieselAllocation);
    }

    public function update(UpdateDieselAllocationRequest $request, DieselAllocation $dieselAllocation)
    {
        $dieselAllocation->update($request->validated());
        return new DieselAllocationResource($dieselAllocation);
    }

    public function destroy(DieselAllocation $dieselAllocation)
    {
        $dieselAllocation->delete();
        return response()->json(['message' => 'Diesel allocation deleted'], 200);
    }

    public function bulkStore(BulkStoreDieselAllocationRequest $request)
    {
        $dieselAllocationsData = $request->validated()['dieselAllocations'];
        
        $dieselAllocations = collect($dieselAllocationsData)->map(function ($dieselAllocationData) {
            return DieselAllocation::create($dieselAllocationData);
        });

        return DieselAllocationResource::collection($dieselAllocations);
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDieselAllocationTypeRequest;
use App\Http\Requests\UpdateDieselAllocationTypeRequest;
use App\Http\Resources\DieselAllocationTypeResource;
use App\Models\DieselAllocationType;
use Illuminate\Http\Request;

class DieselAllocationTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = $request->query('paging', 'true') === 'false'
            ? DieselAllocationType::all()
            : DieselAllocationType::paginate(10);

        return DieselAllocationTypeResource::collection($types);
    }

    public function store(StoreDieselAllocationTypeRequest $request)
    {
        $type = DieselAllocationType::create($request->validated());
        return new DieselAllocationTypeResource($type);
    }

    public function show(DieselAllocationType $dieselAllocationType)
    {
        return new DieselAllocationTypeResource($dieselAllocationType);
    }

    public function update(UpdateDieselAllocationTypeRequest $request, DieselAllocationType $dieselAllocationType)
    {
        $dieselAllocationType->update($request->validated());
        return new DieselAllocationTypeResource($dieselAllocationType);
    }

    public function destroy(DieselAllocationType $dieselAllocationType)
    {
        $dieselAllocationType->delete();
        return response()->json(['message' => 'Allocation type deleted'], 200);
    }
}

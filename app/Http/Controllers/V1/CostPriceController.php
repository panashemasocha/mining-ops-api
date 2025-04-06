<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreCostPriceRequest;
use App\Http\Requests\UpdateCostPriceRequest;
use App\Http\Resources\CostPriceResource;
use App\Models\CostPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CostPriceController extends Controller
{
    public function index(Request $request)
    {
        $costPrices = $request->query('paging', 'true') === 'false' 
            ? CostPrice::all() 
            : CostPrice::paginate(10);
        return CostPriceResource::collection($costPrices);
    }

    public function store(StoreCostPriceRequest $request)
    {
        $costPrice = CostPrice::create($request->validated());
        return new CostPriceResource($costPrice);
    }

    public function show($id)
    {
        $costPrice = CostPrice::findOrFail($id);
        return new CostPriceResource($costPrice);
    }

    public function update(UpdateCostPriceRequest $request, $id)
    {
        $costPrice = CostPrice::findOrFail($id);
        $costPrice->update($request->validated());
        return new CostPriceResource($costPrice);
    }

    public function destroy($id)
    {
        $costPrice = CostPrice::findOrFail($id);
        $costPrice->delete();
        return response()->json(['message' => 'Cost price deleted'], 200);
    }
}
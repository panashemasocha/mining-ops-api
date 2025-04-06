<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreOreRequest;
use App\Http\Requests\UpdateOreRequest;
use App\Http\Resources\OreResource;
use App\Models\Ore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OreController extends Controller
{
    public function index(Request $request)
    {
        $ores = $request->query('paging', 'true') === 'false' 
            ? Ore::all() 
            : Ore::paginate(10);
        return OreResource::collection($ores);
    }

    public function store(StoreOreRequest $request)
    {
        $ore = Ore::create($request->validated());
        return new OreResource($ore);
    }

    public function show($id)
    {
        $ore = Ore::findOrFail($id);
        return new OreResource($ore);
    }

    public function update(UpdateOreRequest $request, $id)
    {
        $ore = Ore::findOrFail($id);
        $ore->update($request->validated());
        return new OreResource($ore);
    }

    public function destroy($id)
    {
        $ore = Ore::findOrFail($id);
        $ore->delete();
        return response()->json(['message' => 'Ore deleted'], 200);
    }
}
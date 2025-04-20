<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreOreRequest;
use App\Http\Requests\UpdateOreRequest;
use App\Http\Resources\OreQuantityResource;
use App\Http\Resources\OreResource;
use App\Models\Ore;
use App\Repositories\OreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OreController extends Controller
{
    protected $oreRepository;

    public function __construct(OreRepository $oreRepository)
    {
        $this->oreRepository = $oreRepository;
    }

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

    public function quantities(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
         $query = Ore::select([
            'ores.*',
            DB::raw('ores.quantity - COALESCE((
                    SELECT SUM(d.ore_quantity)
                    FROM dispatches AS d
                    WHERE d.ore_id = ores.id
                      AND d.status = "accepted"
                ), 0) AS remaining_quantity')
        ])
            ->orderBy('remaining_quantity', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        $paginator = $query->paginate($perPage);

        return OreQuantityResource::collection($paginator);
    }
}
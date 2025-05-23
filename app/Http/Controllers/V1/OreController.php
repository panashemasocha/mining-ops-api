<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreOreRequest;
use App\Http\Requests\UpdateOreRequest;
use App\Http\Resources\OreQuantityResource;
use App\Http\Resources\OreResource;
use App\Models\Ore;
use App\Repositories\OreRepository;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OreController extends Controller
{
    protected $fcmService;


    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
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

        // Notify relevant users about new ore data
        $this->fcmService->sendToHigherRanking(
            ['manager', 'supervisor', 'admin'], // Higher ranking roles
            'New Ore Data Submitted',
            'New ore data has been submitted for review.',
            [
                'ore_id' => $ore->id,
                'notification_type' => 'new_ore',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        );

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
        $perPage = $request->input('per_page', 10);
        $ores = Ore::select([
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
            ->paginate($perPage);

        return OreQuantityResource::collection($ores);
    }
}
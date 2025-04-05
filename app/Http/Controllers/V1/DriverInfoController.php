<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\DriverInfoStoreRequest;
use App\Http\Requests\DriverInfoUpdateRequest;
use App\Http\Requests\StoreDriverInfoRequest;
use App\Http\Requests\UpdateDriverInfoRequest;
use App\Http\Resources\DriverInfoResource;
use App\Models\DriverInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class DriverInfoController extends Controller
{
    public function index(Request $request)
    {
        $driverInfos = $request->query('paging', 'true') === 'false'
            ? DriverInfo::all()
            : DriverInfo::paginate(10);
        return DriverInfoResource::collection($driverInfos);
    }

    public function store(StoreDriverInfoRequest $request)
    {
        $driverInfo = DriverInfo::create($request->validated());
        return new DriverInfoResource($driverInfo);
    }

    public function show($id)
    {
        $driverInfo = DriverInfo::findOrFail($id);
        return new DriverInfoResource($driverInfo);
    }

    public function update(UpdateDriverInfoRequest $request, $id)
    {
        $driverInfo = DriverInfo::findOrFail($id);
        $driverInfo->update($request->validated());
        return new DriverInfoResource($driverInfo);
    }

    public function destroy($id)
    {
        $driverInfo = DriverInfo::findOrFail($id);
        $driverInfo->delete();
        return response()->json(['message' => 'Driver info deleted'], 200);
    }
}
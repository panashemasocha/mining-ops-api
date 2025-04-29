<?php
namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFundingRequest;
use App\Http\Requests\UpdateFundingRequest;
use App\Http\Resources\FundingRequestResource;
use App\Models\FundingRequest;
use Illuminate\Http\Request;

class FundingRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = FundingRequest::orderBy('updated_at', 'desc');
        $requests = $request->query('paging', 'true') === 'false'
            ? $query->get()
            : $query->paginate(10);

        return FundingRequestResource::collection($requests);
    }

    public function store(StoreFundingRequest $request)
    {
        $funding = FundingRequest::create($request->validated());
        return new FundingRequestResource($funding);
    }

    public function show($id)
    {
        $funding = FundingRequest::findOrFail($id);
        return new FundingRequestResource($funding);
    }

    public function update(UpdateFundingRequest $request, $id)
    {
        $funding = FundingRequest::findOrFail($id);
        $funding->update($request->validated());
        return new FundingRequestResource($funding);
    }

    public function destroy($id)
    {
        $funding = FundingRequest::findOrFail($id);
        $funding->delete();
        return response()->json(['message' => 'Funding request deleted'], 200);
    }
}
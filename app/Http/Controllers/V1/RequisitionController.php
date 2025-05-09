<?php

namespace App\Http\Controllers\V1;

use App\Filters\V1\RequisitionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFundingRequest;
use App\Http\Requests\UpdateFundingRequest;
use App\Http\Resources\FundingRequestResource;
use App\Models\FundingRequest;
use App\Services\RequisitionStatsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the funding requests with optional filtering and stats.
     *
     * @param Request $request
     * @param RequisitionStatsService $statsService
     * @return JsonResponse
     */
    public function index(Request $request, RequisitionStatsService $statsService)
    {
        // Apply filters
        $filter = new RequisitionFilter();
        $filterItems = $filter->transform($request);

        $query = FundingRequest::where($filterItems);
        $query->orderBy('updated_at', 'desc');

        // Get requisitions with pagination
        $requests = $request->query('paging', 'true') === 'false'
            ? $query->get()
            : $query->paginate(10)->appends($request->query());

        // Get stats
        $stats = $statsService->getStats();

        // Return structured response
        return response()->json([
            'data' => [
                'stats' => $stats,
                'requisitions' => FundingRequestResource::collection($requests)
            ]
        ]);
    }

    /**
     * Store a newly created funding request.
     *
     * @param StoreFundingRequest $request
     * @return FundingRequestResource
     */
    public function store(StoreFundingRequest $request)
    {
        $funding = FundingRequest::create($request->validated());

        return new FundingRequestResource($funding);
    }

    /**
     * Display the specified funding request.
     *
     * @param int $id
     * @return FundingRequestResource
     */
    public function show($id)
    {
        $funding = FundingRequest::findOrFail($id);

        return new FundingRequestResource($funding);
    }

    /**
     * Update the specified funding request.
     *
     * @param UpdateFundingRequest $request
     * @param int $id
     * @return FundingRequestResource
     */
    public function update(UpdateFundingRequest $request, $id)
    {
        $funding = FundingRequest::findOrFail($id);
        $funding->update($request->validated());

        return new FundingRequestResource($funding);
    }

    /**
     * Remove the specified funding request.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $funding = FundingRequest::findOrFail($id);
        $funding->delete();

        return response()->json(['message' => 'Funding request deleted'], 200);
    }
}
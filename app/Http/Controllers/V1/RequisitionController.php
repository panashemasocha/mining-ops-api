<?php

namespace App\Http\Controllers\V1;

use App\Filters\V1\RequisitionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFundingRequest;
use App\Http\Requests\UpdateFundingRequest;
use App\Http\Resources\FundingRequestResource;
use App\Models\Account;
use App\Models\FundingRequest;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use App\Services\RequisitionStatsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
        $isPaginated = $request->query('paging', 'true') !== 'false';

        if ($isPaginated) {
            $requests = $query->paginate(10)->appends($request->query());
            $requisitions = FundingRequestResource::collection($requests);

        } else {
            $requests = $query->get();
            $requisitions = FundingRequestResource::collection($requests);
        }

        // Get stats
        $stats = $statsService->getStats();

        // Return structured response
        return response()->json([
            'data' => [
                'stats' => $stats,
                'requisitions' => $requisitions
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

        $prevStatus = $funding->status;
        $funding->update($request->validated());

        // If it just became accepted, post the GL transaction
        if ($prevStatus !== 'accepted' && $funding->status === 'accepted') {
            $this->postFundingTransaction($funding);
        }

        return new FundingRequestResource($funding);
    }

    /**
     * When a funding request is accepted, record
     * the GL transaction and its entries.
     */
    protected function postFundingTransaction(FundingRequest $funding): void
    {
        // Equity account ID
        $equityAccount = Account::where('id', 8)->firstOrFail();
        DB::transaction(function () use ($funding, $equityAccount) {
            // 1) Create the GL transaction
            $tx = GLTransaction::create([
                'trans_date' => $funding->decision_date
                    ? Carbon::parse($funding->decision_date)->toDateString()
                    : Carbon::now()->toDateString(),
                'description' => "Requisition approved – {$funding->purpose}",
                'created_by' => $funding->accountant_id ?? auth()->id(),
                'trans_type' => 'requisition',
            ]);

            // 2) Debit the funded current‐asset account
            GLEntry::create([
                'trans_id' => $tx->id,
                'account_id' => $funding->account_id,
                'debit_amt' => $funding->amount,
                'credit_amt' => 0,
            ]);

            // 3) Credit the equity account
            GLEntry::create([
                'trans_id' => $tx->id,
                'account_id' => $equityAccount->id,
                'debit_amt' => 0,
                'credit_amt' => $funding->amount,
            ]);
        });
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
<?php
namespace App\Repositories;

use App\Models\FundingRequest;
use Carbon\Carbon;

class RequisitionRepository
{
    /**
     * Sum of all accepted funding requests up to (and including) a given date.
     */
    public function getTotalAcceptedUpTo(Carbon $asOfDate): float
    {
        return FundingRequest::where('status', 'accepted')
            ->whereDate('decision_date', '<=', $asOfDate->toDateString())
            ->sum('amount') ?? 0.0;
    }

    /**
     * Sum of all accepted funding requests between two dates (inclusive).
     */
    public function getTotalAcceptedBetween(Carbon $start, Carbon $end): float
    {
        return FundingRequest::where('status', 'accepted')
            ->whereBetween('decision_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount') ?? 0.0;
    }
}

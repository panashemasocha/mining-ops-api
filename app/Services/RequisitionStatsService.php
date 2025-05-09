<?php

namespace App\Services;

use App\Models\FundingRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RequisitionStatsService
{
    /**
     * Get the statistics for funding requests.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'today' => $this->getTodayStats(),
            'thisWeek' => $this->getThisWeekStats(),
            'thisMonth' => $this->getThisMonthStats(),
            'overall' => $this->getOverallStats(),
        ];
    }

    /**
     * Get today's statistics.
     *
     * @return array
     */
    protected function getTodayStats(): array
    {
        $today = Carbon::today();

        return $this->getStatsByDateRange($today, $today->copy()->endOfDay());
    }

    /**
     * Get this week's statistics.
     *
     * @return array
     */
    protected function getThisWeekStats(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $this->getStatsByDateRange($startOfWeek, $endOfWeek);
    }

    /**
     * Get this month's statistics.
     *
     * @return array
     */
    protected function getThisMonthStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return $this->getStatsByDateRange($startOfMonth, $endOfMonth);
    }

    /**
     * Get overall statistics.
     *
     * @return array
     */
    protected function getOverallStats(): array
    {
        return [
            'pending' => FundingRequest::where('status', 'pending')->count(),
            'accepted' => FundingRequest::where('status', 'accepted')->count(),
            'rejected' => FundingRequest::where('status', 'rejected')->count(),
        ];
    }

    /**
     * Get statistics by date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    protected function getStatsByDateRange(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'pending' => FundingRequest::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'accepted' => FundingRequest::where('status', 'accepted')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'rejected' => FundingRequest::where('status', 'rejected')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];
    }
}
<?php

namespace App\Repositories;

use App\Models\Ore;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OreRepository
{
    /**
     * Get all ores within a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOres($startDate, $endDate)
    {
        return Ore::whereBetween('created_at', [$startDate, $endDate])
            ->with(['supplier', 'oreType', 'oreQualityType', 'oreQualityGrade', 'creator'])
            ->get();
    }

    /**
     * Get ores for a specific site clerk within a date range.
     *
     * @param int $siteClerkId
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOresForSiteClerk($siteClerkId, $startDate, $endDate)
    {
        return Ore::whereBetween('created_at', [$startDate, $endDate])
            ->where('created_by', $siteClerkId)
            ->with(['supplier', 'oreType', 'oreQualityType', 'oreQualityGrade', 'creator'])
            ->get();
    }

    /**
     * Get ore quantity statistics by type for all ores within a date range.
     */
    public function getOreQuantityStats($startDate, $endDate)
    {
        // Normalize to full-day Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 1) Submitted from ores table
        $submittedOres = Ore::whereBetween('ores.created_at', [$start, $end])
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(ores.quantity) as submitted'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        // 2) Collected from fulfilled trips
        $collectedOres = Trip::where('trips.status', 'fulfilled')
            ->whereBetween('trips.created_at', [$start, $end])
            ->join('dispatches', 'trips.dispatch_id', '=', 'dispatches.id')
            ->join('ores', 'dispatches.ore_id', '=', 'ores.id')
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(trips.ore_quantity) as collected'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        return $this->combineStats($submittedOres, $collectedOres);
    }

    /**
     * Same as above but filtered to a specific site clerk.
     */
    public function getOreQuantityStatsForSiteClerk($siteClerkId, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 1) Submitted
        $submittedOres = Ore::where('created_by',$siteClerkId)
            ->whereBetween('ores.created_at', [$start, $end])
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(ores.quantity) as submitted'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        // 2) Collected
        $collectedOres = Trip::where('trips.status', 'fulfilled')
            ->whereBetween('trips.created_at', [$start, $end])
            ->join('dispatches', function ($join) use ($siteClerkId) {
                $join->on('trips.dispatch_id', '=', 'dispatches.id')
                    ->where('dispatches.site_clerk_id', $siteClerkId);
            })
            ->join('ores', 'dispatches.ore_id', '=', 'ores.id')
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(trips.ore_quantity) as collected'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        return $this->combineStats($submittedOres, $collectedOres);
    }

    /**
     * Combines submitted & collected arrays into the final per‑type structure.
     */
    protected function combineStats(array $submitted, array $collected): array
    {
        $allTypes = array_unique(
            array_merge(array_keys($submitted), array_keys($collected))
        );

        $stats = [];
        foreach ($allTypes as $type) {
            $stats[] = [
                'type' => $type,
                'submitted' => $submitted[$type]['submitted'] ?? 0,
                'collected' => $collected[$type]['collected'] ?? 0,
            ];
        }

        return $stats;
    }

    /**
     * Generate comprehensive ore quantity stats for: all‑time, this month, this week, today.
     */
    public function generateOreQuantityStats($siteClerkId = null)
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // All time (back to year 2000)
        $allTimeStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, '2000-01-01', $today)
            : $this->getOreQuantityStats('2000-01-01', $today);

        // Month → today
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, $monthStart, $today)
            : $this->getOreQuantityStats($monthStart, $today);

        // Week → today
        $weekStart = $now->copy()->startOfWeek()->toDateString();
        $weekStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, $weekStart, $today)
            : $this->getOreQuantityStats($weekStart, $today);

        // Today only (use whereDate to be extra explicit)
        $todayStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, $today, $today)
            : $this->getOreQuantityStats($today, $today);

        return [
            'oreType' => $allTimeStats,
            'currentMonth' => [
                'label' => $now->format('F Y'),
                'oreType' => $monthStats,
            ],
            'currentWeek' => [
                'label' => 'Week ' . $now->weekOfMonth,
                'oreType' => $weekStats,
            ],
            'today' => [
                'label' => $today,
                'oreType' => $todayStats,
            ],
        ];
    }
}
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
        return Ore::whereHas('dispatches', function ($query) use ($siteClerkId) {
            $query->where('site_clerk_id', $siteClerkId);
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['supplier', 'oreType', 'oreQualityType', 'oreQualityGrade', 'creator'])
            ->get();
    }

    /**
     * Get ore quantity statistics by type for all ores within a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getOreQuantityStats($startDate, $endDate)
    {
        // Get submitted ore quantities (from Ore table)
        $submittedOres = Ore::whereBetween('ores.created_at', [$startDate, $endDate])
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(ores.quantity) as submitted'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        // Get collected ore quantities (from fulfilled trips)
        $collectedOres = Trip::where('trips.status', 'fulfilled')
            ->whereBetween('trips.created_at', [$startDate, $endDate])
            ->join('dispatches', 'trips.dispatch_id', '=', 'dispatches.id')
            ->join('ores', 'dispatches.ore_id', '=', 'ores.id')
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(trips.ore_quantity) as collected'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        // Combine both datasets
        $oreTypeStats = [];
        $allTypes = array_unique(array_merge(array_keys($submittedOres), array_keys($collectedOres)));

        foreach ($allTypes as $type) {
            $oreTypeStats[] = [
                'type' => $type,
                'submitted' => isset($submittedOres[$type]) ? round($submittedOres[$type]['submitted'], 2) : 0,
                'collected' => isset($collectedOres[$type]) ? round($collectedOres[$type]['collected'], 2) : 0
            ];
        }

        return $oreTypeStats;
    }

    /**
     * Get ore quantity statistics by type for a specific site clerk within a date range.
     *
     * @param int $siteClerkId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getOreQuantityStatsForSiteClerk($siteClerkId, $startDate, $endDate)
    {
        // Get submitted ore quantities (from Ore table)
        $submittedOres = Ore::whereHas('dispatches', function ($q) use ($siteClerkId) {
            $q->where('site_clerk_id', $siteClerkId);
        })
            ->whereBetween('ores.created_at', [$startDate, $endDate])
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(ores.quantity) as submitted'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        // Get collected ore quantities (from fulfilled trips)
        $collectedOres = Trip::where('trips.status', 'fulfilled')
            ->whereBetween('trips.created_at', [$startDate, $endDate])
            ->join('dispatches', 'trips.dispatch_id', '=', 'dispatches.id')
            ->where('dispatches.site_clerk_id', $siteClerkId)
            ->join('ores', 'dispatches.ore_id', '=', 'ores.id')
            ->join('ore_types', 'ores.ore_type_id', '=', 'ore_types.id')
            ->select('ore_types.type', DB::raw('SUM(trips.ore_quantity) as collected'))
            ->groupBy('ore_types.type')
            ->get()
            ->keyBy('type')
            ->toArray();

        // Combine both datasets
        $oreTypeStats = [];
        $allTypes = array_unique(array_merge(array_keys($submittedOres), array_keys($collectedOres)));

        foreach ($allTypes as $type) {
            $oreTypeStats[] = [
                'type' => $type,
                'submitted' => isset($submittedOres[$type]) ? round($submittedOres[$type]['submitted'], 2) : 0,
                'collected' => isset($collectedOres[$type]) ? round($collectedOres[$type]['collected'], 2) : 0
            ];
        }

        return $oreTypeStats;
    }

    /**
     * Generate comprehensive ore quantity statistics.
     *
     * @param string|null $siteClerkId (Optional) Site clerk ID for filtering
     * @return array
     */
    public function generateOreQuantityStats($siteClerkId = null)
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $startOfWeek = $now->copy()->startOfWeek()->format('Y-m-d');
        $currentWeekNumber = $now->weekOfMonth;

        // Get stats for all time
        $allTimeOreStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, '2000-01-01', $today)
            : $this->getOreQuantityStats('2000-01-01', $today);

        // Get stats for current month
        $currentMonthOreStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, $startOfMonth, $today)
            : $this->getOreQuantityStats($startOfMonth, $today);

        // Get stats for current week
        $currentWeekOreStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, $startOfWeek, $today)
            : $this->getOreQuantityStats($startOfWeek, $today);

        // Get stats for today
        $todayOreStats = $siteClerkId
            ? $this->getOreQuantityStatsForSiteClerk($siteClerkId, $today, $today)
            : $this->getOreQuantityStats($today, $today);

        return [
            'oreType' => $allTimeOreStats,
            'currentMonth' => [
                'label' => $now->format('F Y'),
                'oreType' => $currentMonthOreStats
            ],
            'currentWeek' => [
                'label' => 'Week ' . $currentWeekNumber,
                'oreType' => $currentWeekOreStats
            ],
            'today' => [
                'label' => $today,
                'oreType' => $todayOreStats
            ]
        ];
    }
}
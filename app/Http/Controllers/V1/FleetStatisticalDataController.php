<?php

namespace App\Http\Controllers\V1;

use App\Repositories\DieselAllocationRepository;
use App\Repositories\DieselAllocationTypeRepository;
use App\Repositories\OdometerReadingRepository;
use App\Repositories\TripRepository;
use App\Repositories\VehicleRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class FleetStatisticalDataController extends Controller
{
    protected $tripRepository;
    protected $vehicleRepository;
    protected $dieselAllocationTypeRepository;
    protected $dieselAllocationRepository;
    protected $odometerReadingRepository;

    public function __construct(
        TripRepository $tripRepository,
        VehicleRepository $vehicleRepository,
        DieselAllocationTypeRepository $dieselAllocationTypeRepository,
        DieselAllocationRepository $dieselAllocationRepository,
        OdometerReadingRepository $odometerReadingRepository
    ) {
        $this->tripRepository = $tripRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->dieselAllocationTypeRepository = $dieselAllocationTypeRepository;
        $this->dieselAllocationRepository = $dieselAllocationRepository;
        $this->odometerReadingRepository = $odometerReadingRepository;
    }

    /**
     * Get fleet statistics for the last 3 months
     *
     * @return JsonResponse
     */
    public function getFleetStatistics(): JsonResponse
    {
        // Get dates for last 3 months
        $currentDate = Carbon::now();
        $months = $this->getLastThreeMonths($currentDate);

        // Calculate statistics
        $statistics = [
            'fleetStatus' => $this->getFleetStatusStatistics(),
            'vehicleDistribution' => $this->getVehicleDistributionStatistics(),
            'monthlyStatistics' => $this->getMonthlyStatistics($months),
            'mileageStatistics' => $this->getMileageStatistics(),
            'dieselStatistics' => $this->getDieselStatistics($months)
        ];

        return response()->json(['data' => $statistics]);
    }

    /**
     * Get fleet status statistics (in transit, available, etc.)
     *
     * @return array
     */
    private function getFleetStatusStatistics(): array
    {
        $vehicles = $this->vehicleRepository->getAllVehicles();

        $inTransit = $vehicles->where('status', 'active trip')->count();
        $available = $vehicles->where('status', 'off trip')->count();
        $maintenance = $vehicles->where('status', 'maintenance')->count();
        $inactive = $vehicles->where('status', 'inactive')->count();

        $subTypeStats = [];
        $vehicleSubTypes = [
            1 => 'passenger',
            2 => 'utility',
            3 => 'haulage',
            4 => 'excavation',
            5 => 'support'
        ];

        foreach ($vehicleSubTypes as $id => $type) {
            $subTypeStats[$type] = [
                'total' => $vehicles->where('sub_type_id', $id)->count(),
                'inTransit' => $vehicles->where('sub_type_id', $id)->where('status', 'active trip')->count(),
                'available' => $vehicles->where('sub_type_id', $id)->where('status', 'off trip')->count(),
                'maintenance' => $vehicles->where('sub_type_id', $id)->where('status', 'maintenance')->count(),
                'inactive' => $vehicles->where('sub_type_id', $id)->where('status', 'inactive')->count()
            ];
        }

        return [
            'totalVehicles' => $vehicles->count(),
            'inTransit' => $inTransit,
            'available' => $available,
            'maintenance' => $maintenance,
            'inactive' => $inactive,
            'bySubType' => $subTypeStats
        ];
    }

    /**
     * Get vehicle distribution by category and subtype
     *
     * @return array
     */
    private function getVehicleDistributionStatistics(): array
    {
        $vehicles = $this->vehicleRepository->getAllVehicles();

        $categoryDistribution = [];
        foreach ($vehicles->groupBy('category_id') as $categoryId => $categoryVehicles) {
            $category = $categoryVehicles->first()->vehicleCategory->name ?? "Unknown";
            $categoryDistribution[$category] = $categoryVehicles->count();
        }

        $subTypeDistribution = [];
        foreach ($vehicles->groupBy('sub_type_id') as $subTypeId => $subTypeVehicles) {
            $subType = $subTypeVehicles->first()->vehicleSubType->name ?? "Unknown";
            $subTypeDistribution[$subType] = $subTypeVehicles->count();
        }

        return [
            'byCategory' => $categoryDistribution,
            'bySubType' => $subTypeDistribution
        ];
    }

    /**
     * Get monthly statistics for the last 3 months
     *
     * @param array $months Array containing month periods
     * @return array
     */
    private function getMonthlyStatistics(array $months): array
    {
        $list = [];

        foreach ($months as $month) {
            $trips = $this->tripRepository->getTrips($month['startDate'], $month['endDate']);

            // count by vehicle_id
            $byVehicle = $trips->countBy('vehicle_id')->toArray();
            $maxCount = !empty($byVehicle) ? max($byVehicle) : 0;
            $maxVid = !empty($byVehicle) ? array_search($maxCount, $byVehicle) : null;
            $reg = null;

            if ($maxVid) {
                $veh = $this->vehicleRepository->getVehicleById($maxVid);
                $reg = $veh ? $veh->reg_number : null;
            }

            $list[] = [
                'month' => $month['name'],
                'totalTrips' => $trips->count(),
                'mostTrips' => [
                    'count' => $maxCount,
                    'vehicle' => $reg,
                ],
            ];
        }

        return $list;
    }

    /**
     * Get mileage statistics (all in Kilometers)
     *
     * @return array
     */
    private function getMileageStatistics(): array
    {
        $vehicles = $this->vehicleRepository->getAllVehicles();
        $vehicleMileage = [];

        foreach ($vehicles as $vehicle) {
            $readings = $vehicle->odometerReading;

            // Sum up converted distances
            $totalKm = $readings->reduce(function ($carry, $r) {
                $delta = max(0, $r->trip_end_value - $r->initial_value);

                // Convert to km if needed
                if ($r->reading_unit === 'Mile') {
                    $delta *= 1.60934;
                }

                return $carry + $delta;
            }, 0.0);

            $vehicleMileage[$vehicle->id] = [
                'regNumber' => $vehicle->reg_number,
                'mileage' => round($totalKm, 2),   //2.dp
                'readingUnit' => 'Kilometers',
            ];
        }

        // Initialize highest/lowest
        $highest = ['mileage' => 0, 'regNumber' => null, 'readingUnit' => 'Kilometers'];
        $lowest = ['mileage' => PHP_INT_MAX, 'regNumber' => null, 'readingUnit' => 'Kilometers'];

        foreach ($vehicleMileage as $data) {
            // Highest
            if ($data['mileage'] > $highest['mileage']) {
                $highest = $data;
            }
            // Lowest (ignore zero-mileage unless it's the only data)
            if ($data['mileage'] > 0 && $data['mileage'] < $lowest['mileage']) {
                $lowest = $data;
            }
        }

        // If nothing had mileage, set lowest to zero
        if ($lowest['mileage'] === PHP_INT_MAX) {
            $lowest = ['mileage' => 0, 'regNumber' => null, 'readingUnit' => 'Kilometers'];
        }

        return [
            'highestMileage' => $highest,
            'lowestMileage' => $lowest,
        ];
    }


    /**
     * Get diesel statistics for the last 3 months
     *
     * @param array $months Array containing month periods
     * @return array
     */
    private function getDieselStatistics(array $months): array
    {
        $list = [];

        // pre‐fetch all excavation IDs
        $excavationIds = $this->vehicleRepository
            ->getAllVehicles()
            ->where('sub_type_id', 4)
            ->pluck('id')
            ->toArray();

        foreach ($months as $month) {
            $allocs = $this->dieselAllocationRepository
                ->getDieselAllocations($month['startDate'], $month['endDate']);

            $totalDiesel = $allocs->sum('litres');

            // group & find highest by vehicle
            $byVeh = [];
            foreach ($allocs->groupBy('vehicle_id') as $vid => $group) {
                $sum = $group->sum('litres');
                $veh = $this->vehicleRepository->getVehicleById($vid);
                if ($veh) {
                    $byVeh[$vid] = ['regNumber' => $veh->reg_number, 'litres' => $sum];
                }
            }

            $highest = ['litres' => 0, 'regNumber' => null];
            foreach ($byVeh as $d) {
                if ($d['litres'] > $highest['litres']) {
                    $highest = $d;
                }
            }

            $excavationDiesel = $allocs->whereIn('vehicle_id', $excavationIds)->sum('litres');

            $list[] = [
                'month' => $month['name'],
                'dieselUsed' => $totalDiesel,
                'highestUsagePerVehicle' => $highest,
                'excavationDiesel' => $excavationDiesel,
            ];
        }

        return [
            'monthlyStats' => $list,
        ];
    }

    /**
     * Helper method to get date ranges for the last 3 months
     *
     * @param Carbon $currentDate
     * @return array
     */
    private function getLastThreeMonths(Carbon $currentDate): array
    {
        $months = [];

        for ($i = 0; $i < 3; $i++) {
            $date = clone $currentDate;
            $date->subMonths($i);

            $startOfMonth = clone $date;
            $startOfMonth->startOfMonth();

            $endOfMonth = clone $date;
            $endOfMonth->endOfMonth();

            $months[] = [
                'name' => $date->format('F Y'),
                'startDate' => $startOfMonth->format('Y-m-d'),
                'endDate' => $endOfMonth->format('Y-m-d')
            ];
        }

        return $months;
    }
}
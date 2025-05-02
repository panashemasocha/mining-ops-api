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
        $monthlyStats = [];

        foreach ($months as $month) {
            $trips = $this->tripRepository->getTrips($month['startDate'], $month['endDate']);

            // Group trips by vehicle
            $tripsByVehicle = $trips
                ->countBy('vehicle_id')
                ->toArray();

            // Find vehicle with most trips
            $mostTripsVehicleId = !empty($tripsByVehicle) ? array_search(max($tripsByVehicle), $tripsByVehicle) : null;
            $mostTripsCount = !empty($tripsByVehicle) ? max($tripsByVehicle) : 0;

            $mostTripsVehicleReg = null;
            if ($mostTripsVehicleId) {
                $vehicle = $this->vehicleRepository->getVehicleById($mostTripsVehicleId);
                $mostTripsVehicleReg = $vehicle ? $vehicle->reg_number : null;
            }

            $monthlyStats[$month['name']] = [
                'mostTrips' => [
                    'count' => $mostTripsCount,
                    'vehicle' => $mostTripsVehicleReg,
                ],
                'totalTrips' => $trips->count()
            ];
        }

        return $monthlyStats;
    }

    /**
     * Get mileage statistics
     *
     * @return array
     */
    private function getMileageStatistics(): array
    {
        $vehicles = $this->vehicleRepository->getAllVehicles();
        $vehicleMileage = [];

        foreach ($vehicles as $vehicle) {
            $odometerReadings = $vehicle->odometerReading;
            $totalMileage = $odometerReadings->sum('distance_travelled');

            $vehicleMileage[$vehicle->id] = [
                'regNumber' => $vehicle->reg_number,
                'mileage' => $totalMileage
            ];
        }

        // Find highest and lowest mileage
        $highest = ['mileage' => 0, 'regNumber' => null];
        $lowest = ['mileage' => PHP_INT_MAX, 'regNumber' => null];

        foreach ($vehicleMileage as $vehicleData) {
            if ($vehicleData['mileage'] > $highest['mileage']) {
                $highest['mileage'] = $vehicleData['mileage'];
                $highest['regNumber'] = $vehicleData['regNumber'];
            }

            if ($vehicleData['mileage'] < $lowest['mileage'] && $vehicleData['mileage'] > 0) {
                $lowest['mileage'] = $vehicleData['mileage'];
                $lowest['regNumber'] = $vehicleData['regNumber'];
            }
        }

        // If no vehicles have mileage, set lowest to 0
        if ($lowest['mileage'] === PHP_INT_MAX) {
            $lowest['mileage'] = 0;
        }

        return [
            'highestMileage' => $highest,
            'lowestMileage' => $lowest
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
        $monthlyDieselStats = [];
        $excavationVehicles = $this->vehicleRepository->getAllVehicles()->where('sub_type_id', 4);
        $excavationVehicleIds = $excavationVehicles->pluck('id')->toArray();

        foreach ($months as $month) {
            $dieselAllocations = $this->dieselAllocationRepository->getDieselAllocations($month['startDate'], $month['endDate']);

            // Total diesel used this month
            $totalDiesel = $dieselAllocations->sum('litres');

            // Group by vehicle to find highest usage
            $dieselByVehicle = [];
            foreach ($dieselAllocations->groupBy('vehicle_id') as $vehicleId => $allocations) {
                $totalLitres = $allocations->sum('litres');
                $vehicle = $this->vehicleRepository->getVehicleById($vehicleId);

                if ($vehicle) {
                    $dieselByVehicle[$vehicleId] = [
                        'regNumber' => $vehicle->reg_number,
                        'litres' => $totalLitres
                    ];
                }
            }

            // Find vehicle with highest diesel usage
            $highestUsage = ['litres' => 0, 'regNumber' => null];
            foreach ($dieselByVehicle as $vehicleData) {
                if ($vehicleData['litres'] > $highestUsage['litres']) {
                    $highestUsage['litres'] = $vehicleData['litres'];
                    $highestUsage['regNumber'] = $vehicleData['regNumber'];
                }
            }

            // Calculate diesel used for excavation (ore loading)
            $excavationDiesel = $dieselAllocations->whereIn('vehicle_id', $excavationVehicleIds)->sum('litres');


            $monthlyDieselStats[$month['name']] = [
                'dieselUsed' => $totalDiesel,
                'highestUsagePerVehicle' => $highestUsage,
                'excavationDiesel' => $excavationDiesel,
                
            ];
        }

        // Total diesel allocated to excavation vehicles (across all months)
        $totalExcavationDiesel = 0;
        foreach ($monthlyDieselStats as $monthStats) {
            $totalExcavationDiesel += $monthStats['excavationDiesel'];
        }

        return [
            'monthlyStats' => $monthlyDieselStats,
            'totalLoadingOreDieselVolume' => $totalExcavationDiesel
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
<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\GetConsolidatedDataRequest;
use App\Http\Resources\BranchResource;
use App\Http\Resources\CostPriceResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\JobPositionResource;
use App\Http\Resources\MiningSiteResource;
use App\Http\Resources\OreQualityGradeResource;
use App\Http\Resources\OreQualityTypeResource;
use App\Http\Resources\OreTypeResource;
use App\Http\Resources\UserRoleResource;
use App\Http\Resources\VehicleCategoryResource;
use App\Http\Resources\VehicleSubTypeResource;
use App\Repositories\AccountingRepository;
use App\Repositories\DieselAllocationTypeRepository;
use App\Repositories\MiningSiteRepository;
use App\Repositories\OreQualityGradeRepository;
use App\Repositories\OreQualityTypeRepository;
use App\Repositories\OreRepository;
use App\Repositories\OreTypeRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\DispatchRepository;
use App\Repositories\TripRepository;
use App\Repositories\VehicleCategoryRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\PriceRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\BranchRepository;
use App\Repositories\JobPositionRepository;
use App\Repositories\RoleRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\OreResource;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\DispatchResource;
use App\Http\Resources\TripResource;
use App\Repositories\RequisitionRepository;
use App\Repositories\VehicleSubTypeRepository;
use Carbon\Carbon;

class ConsolidatedDataController extends Controller
{
    protected $oreRepository;
    protected $supplierRepository;
    protected $dispatchRepository;
    protected $tripRepository;
    protected $vehicleRepository;
    protected $priceRepository;
    protected $dieselAllocationTypeRepository;
    protected $departmentRepository;
    protected $branchRepository;
    protected $jobPositionRepository;
    protected $roleRepository;
    protected $accountingRepository;
    protected $oreTypeRepository;
    protected $oreQualityTypeRepository;
    protected $oreQualityGradeRepository;
    protected $vehicleCategoryRepository;
    protected $vehicleSubTypeRepository;
    protected $miningSiteRepository;
    protected $requisitionRepository;

    public function __construct(
        OreRepository $oreRepository,
        SupplierRepository $supplierRepository,
        DispatchRepository $dispatchRepository,
        TripRepository $tripRepository,
        VehicleRepository $vehicleRepository,
        PriceRepository $priceRepository,
        DepartmentRepository $departmentRepository,
        BranchRepository $branchRepository,
        JobPositionRepository $jobPositionRepository,
        RoleRepository $roleRepository,
        AccountingRepository $accountingRepository,
        OreTypeRepository $oreTypeRepository,
        OreQualityGradeRepository $oreQualityGradeRepository,
        OreQualityTypeRepository $oreQualityTypeRepository,
        DieselAllocationTypeRepository $dieselAllocationTypeRepository,
        VehicleCategoryRepository $vehicleCategoryRepository,
        VehicleSubTypeRepository $vehicleSubTypeRepository,
        MiningSiteRepository $miningSiteRepository,
        RequisitionRepository $requisitionRepository,
    ) {
        $this->oreRepository = $oreRepository;
        $this->supplierRepository = $supplierRepository;
        $this->dispatchRepository = $dispatchRepository;
        $this->tripRepository = $tripRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->priceRepository = $priceRepository;
        $this->departmentRepository = $departmentRepository;
        $this->branchRepository = $branchRepository;
        $this->jobPositionRepository = $jobPositionRepository;
        $this->roleRepository = $roleRepository;
        $this->accountingRepository = $accountingRepository;
        $this->oreTypeRepository = $oreTypeRepository;
        $this->oreQualityGradeRepository = $oreQualityGradeRepository;
        $this->oreQualityTypeRepository = $oreQualityTypeRepository;
        $this->dieselAllocationTypeRepository = $dieselAllocationTypeRepository;
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
        $this->vehicleSubTypeRepository = $vehicleSubTypeRepository;
        $this->miningSiteRepository = $miningSiteRepository;
        $this->requisitionRepository = $requisitionRepository;
    }

    /**
     * Get consolidated data based on user role and job position.
     *
     * @param GetConsolidatedDataRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConsolidatedData(GetConsolidatedDataRequest $request)
    {
        $roleId = $request->role_id;
        $jobPositionId = $request->job_position_id;
        $userId = $request->id;
        $startDate = $request->input('start_date')
            ?? Carbon::now()->toDateString();
        $endDate = $request->input('end_date')
            ?? Carbon::now()->toDateString();
        $data = [];

        if ($roleId == 3 && $jobPositionId == 7) {
            $data['ores'] = [
                'data' => OreResource::collection(
                    $this->oreRepository->getOres($startDate, $endDate)
                )
            ];
            $data['dispatches'] = [
                'data' => DispatchResource::collection(
                    $this->dispatchRepository->getDispatches($startDate, $endDate)
                )
            ];
            $data['trips'] = [
                'data' => TripResource::collection(
                    $this->tripRepository->getTrips($startDate, $endDate)
                )
            ];
            $data['prices'] = CostPriceResource::collection(
                $this->priceRepository->getAllPrices()
            );
            $data['dieselAllocationTypes'] = \App\Http\Resources\DieselAllocationTypeResource::collection(
                $this->dieselAllocationTypeRepository->getAllDieselAllocationTypes()
            );
        } else if ($jobPositionId == 4) {
            $data['ores'] = [
                'data' => OreResource::collection(
                    $this->oreRepository->getOres($startDate, $endDate)
                )
            ];
            $data['suppliers'] = ['data' => SupplierResource::collection($this->supplierRepository->getAllSuppliers())];
            $data['oreTypes'] = ['data' => OreTypeResource::collection($this->oreTypeRepository->getOreTypes())];
            $data['oreQualityTypes'] = ['data' => OreQualityTypeResource::collection($this->oreQualityTypeRepository->getAllOreQualityTypes())];
            $data['oreQualityGrades'] = ['data' => OreQualityGradeResource::collection($this->oreQualityGradeRepository->getAllOreQualityGrade())];
        } else if ($jobPositionId == 5) {
            $data['dispatches'] = [
                'data' => DispatchResource::collection(
                    $this->dispatchRepository->getDispatchesForDriver($userId, $startDate, $endDate)
                )
            ];
            $data['trips'] = [
                'data' => TripResource::collection(
                    $this->tripRepository->getTripsForDriver($userId, $startDate, $endDate)
                )
            ];
        } else if (in_array($roleId, [1, 2, 3])) {
            $data = $this->getComprehensiveData($request, $startDate, $endDate);
        } else {
            return response()->json(['error' => 'Unauthorized or invalid job position'], 403);
        }

        return response()->json($data, 200);
    }

    /**
     * Retrieve comprehensive data for role IDs 1, 2, or 3 (except Site clerk with roleId 3).
     *
     * @param GetConsolidatedDataRequest $request
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getComprehensiveData(GetConsolidatedDataRequest $request, $startDate, $endDate)
    {
        $data = [
            'dispatches' => [
                'data' => DispatchResource::collection(
                    $this->dispatchRepository->getDispatches($startDate, $endDate)
                )
            ],
            'ores' => [
                'data' => OreResource::collection(
                    $this->oreRepository->getOres($startDate, $endDate)
                )
            ],
            'suppliers' => [
                'data' => SupplierResource::collection(
                    $this->supplierRepository->getAllSuppliers() ?? collect()
                )
            ],
            'trips' => [
                'data' => TripResource::collection(
                    $this->tripRepository->getTrips($startDate, $endDate)
                )
            ],
            'vehicleSubTypes' => [
                'data' => VehicleSubTypeResource::collection(
                    $this->vehicleSubTypeRepository->getAllSubTypes()
                )
            ],
            'vehicleCategories' => [
                'data' => VehicleCategoryResource::collection(
                    $this->vehicleCategoryRepository->getAllCategories()
                )
            ],
            'miningSites' => [
                'data' => MiningSiteResource::collection(
                    $this->miningSiteRepository->getAllSites()
                )
            ],
        ];

        $data['departments'] = DepartmentResource::collection($this->departmentRepository->getAllDepartments());
        $data['branches'] = BranchResource::collection($this->branchRepository->getAllBranches());
        $data['jobPositions'] = JobPositionResource::collection($this->jobPositionRepository->getAllJobPositions());
        $data['roles'] = UserRoleResource::collection($this->roleRepository->getAllRoles());

        // Add financialStats
        $endCarbon = Carbon::parse($endDate);
        $overallCurrentAssets = $this->accountingRepository->getCurrentAssetsBalance($endCarbon);
        $overallCreditors = $this->accountingRepository->getCreditorsBalance($endCarbon);
        $overallPaidExpenses = $this->accountingRepository->getTotalPaidExpenses($startDate, $endDate);
        $overallCashRequisitions = $this->requisitionRepository->getTotalAcceptedUpTo($endCarbon);

        $months = [];
        for ($i = 0; $i < 3; $i++) {
            $monthDate = $endCarbon->copy()->subMonths($i)->startOfMonth();
            $monthStart = $monthDate->copy();
            $monthEnd = $monthDate->copy()->endOfMonth();

            if ($monthDate->month == $endCarbon->month && $monthDate->year == $endCarbon->year) {
                $monthEnd = $endCarbon->copy();
            } else if ($monthEnd > $endCarbon) {
                continue;
            }

            $paidExpenses = $this->accountingRepository->getTotalPaidExpenses($monthStart, $monthEnd);
            $currentAssets = $this->accountingRepository->getCurrentAssetsBalance($monthEnd);
            $creditors = $this->accountingRepository->getCreditorsBalance($monthEnd);
            $monthCashReqs = $this->requisitionRepository->getTotalAcceptedBetween($monthStart, $monthEnd);

            $months[] = [
                'month' => $monthDate->format('F Y'),
                'currentAssets' => number_format($currentAssets, 2, '.', ''),
                'creditors' => number_format($creditors, 2, '.', ''),
                'paidExpenses' => number_format($paidExpenses, 2, '.', ''),
                'cashRequisitions' => number_format($monthCashReqs, 2, '.', ''),
            ];
        }
        $months = array_reverse($months); // Oldest to newest

        $data['financialStats'] = [
            'currentAssets' => number_format($overallCurrentAssets, 2, '.', ''),
            'creditors' => number_format($overallCreditors, 2, '.', ''),
            'paidExpenses' => number_format($overallPaidExpenses, 2, '.', ''),
            'cashRequisitions' => number_format($overallCashRequisitions, 2, '.', ''),
            'monthly' => $months,
        ];

        return $data;
    }
}
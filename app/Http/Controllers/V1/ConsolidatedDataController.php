<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\GetConsolidatedDataRequest;
use App\Http\Requests\ViewCashbookRequest;
use App\Http\Resources\CostPriceResource;
use App\Http\Resources\GLTransactionResource;
use App\Http\Resources\OreQualityGradeResource;
use App\Http\Resources\OreQualityTypeResource;
use App\Http\Resources\OreTypeResource;
use App\Http\Resources\UserRoleResource;
use App\Models\OreQualityType;
use App\Repositories\AccountingRepository;
use App\Repositories\DieselAllocationTypeRepository;
use App\Repositories\DieselAllocationTypeResource;
use App\Repositories\OreQualityGradeRepository;
use App\Repositories\OreQualityTypeRepository;
use App\Repositories\OreRepository;
use App\Repositories\OreTypeRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\DispatchRepository;
use App\Repositories\TripRepository;
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
use App\Http\Resources\VehicleResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\BranchResource;
use App\Http\Resources\JobPositionResource;
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
        $startDate = $request->input(
            'start_date',
            Carbon::now()->toDateString()
        );
        $endDate = $request->input(
            'end_date',
            Carbon::now()->toDateString()
        );
        $data = [];

        if ($roleId == 3 && $jobPositionId == 7) {
            $data['ores'] = OreResource::collection(
                $this->oreRepository->getOres($startDate, $endDate)
            );
            $data['dispatches'] = DispatchResource::collection($this->dispatchRepository->getDispatches($startDate, $endDate));
            $data['prices'] = CostPriceResource::collection($this->priceRepository->getAllPrices());
            $data['dieselAllocationTypes'] = \App\Http\Resources\DieselAllocationTypeResource::collection($this->dieselAllocationTypeRepository->getAllDieselAllocationTypes());
        } else if ($jobPositionId == 4) {

            $data['ores'] = OreResource::collection(
                $this->oreRepository->getOres($startDate, $endDate)
            );
            $data['suppliers'] = ['data' => SupplierResource::collection($this->supplierRepository->getAllSuppliers())];
            $data['oreTypes'] = ['data' => OreTypeResource::collection($this->oreTypeRepository->getOreTypes())];
            $data['oreQualityTypes'] = ['data' => OreQualityTypeResource::collection($this->oreQualityTypeRepository->getAllOreQualityTypes())];
            $data['oreQualityGrades'] = ['data' => OreQualityGradeResource::collection($this->oreQualityGradeRepository->getAllOreQualityGrade())];

        } else if ($jobPositionId == 5) {
            $data['dispatches'] = DispatchResource::collection($this->dispatchRepository->getDispatchesForDriver($userId, $startDate, $endDate));
            $data['trips'] = TripResource::collection($this->tripRepository->getTripsForDriver($userId, $startDate, $endDate));

        } else if (in_array($roleId, [1, 2, 3])) {
            $data = $this->getComprehensiveData($request, $startDate, $endDate);
        } else {
            return response()->json(['error' => 'Unauthorized or invalid job position'], 403);
        }

        return response()->json($data, 200);
    }

    public function cashbook(ViewCashbookRequest $request)
    {
        try {
            $data = $this->accountingRepository
                ->getCashbookTotals($request->start_date, $request->end_date);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json($data);
    }

    /**
     * Retrieve comprehensive data for role IDs 1, 2, or 3 (except Site clerk with roleId 3).
     *
     * @param GetConsolidatedDataRequest $request
     * @return array
     */
    private function getComprehensiveData(GetConsolidatedDataRequest $request, $startDate, $endDate)
    {
        $data = [
            'dispatches' => DispatchResource::collection($this->dispatchRepository->getDispatches($startDate, $endDate)),
            'ores' => OreResource::collection(
                $this->oreRepository->getOres($startDate, $endDate)
            ),
            'suppliers' => ['data' => SupplierResource::collection($this->supplierRepository->getAllSuppliers() ?? collect())],

            'trips' => TripResource::collection($this->tripRepository->getTrips($startDate, $endDate)),

            // 'vehicles' => $this->transformPaginated(
            //     $this->vehicleRepository->getAllVehicles($request->input('vehicles_per_page', 10)),
            //     VehicleResource::class
            // ),
            'financials' => $this->transformPaginated(
                $this->accountingRepository->getAllFinancials($request->input('financials_per_page', 10)),
                GLTransactionResource::class
            ),
        ];

        try {
            $cashbookTotals = $this->accountingRepository
                ->getCashbookTotals(
                    $request->input('start_date'),
                    $request->input('end_date')
                );
        } catch (\RuntimeException $e) {
            $cashbookTotals = [
                'cashReceipts' => 0,
                'cashPayments' => 0,
                'balance' => 0,
            ];
        }

        $data['cashbook'] = $cashbookTotals;

        $data['prices'] = CostPriceResource::collection($this->priceRepository->getAllPrices());
        $data['departments'] = DepartmentResource::collection($this->departmentRepository->getAllDepartments());
        $data['branches'] = BranchResource::collection($this->branchRepository->getAllBranches());
        $data['jobPositions'] = JobPositionResource::collection($this->jobPositionRepository->getAllJobPositions());
        $data['roles'] = UserRoleResource::collection($this->roleRepository->getAllRoles());

        return $data;
    }

    /**
     * Helper method to transform a paginated result using a given resource.
     *
     * @param mixed $result   Either a LengthAwarePaginator (or similar) or a plain Collection.
     * @param string $resourceClass
     * @return array
     */
    private function transformPaginated($result, $resourceClass)
    {
        if (!$result) {
            return ['data' => []];
        }
        // Check if the result is a LengthAwarePaginator instance
        if ($result instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            // Transform the data items using the provided resource.
            $transformedData = $resourceClass::collection($result)->resolve();

            // Extract pagination details.
            $pagination = [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'first_page_url' => $result->url(1),
                'last_page_url' => $result->url($result->lastPage()),
                'next_page_url' => $result->nextPageUrl(),
                'prev_page_url' => $result->previousPageUrl(),
            ];

            return [
                'data' => $transformedData,
                'links' => $pagination,
                'meta' => $pagination,
            ];
        } else {
            // Handle non-paginated results
            return [
                'data' => $resourceClass::collection($result)->resolve()
            ];
        }
    }
}

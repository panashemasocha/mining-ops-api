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
use App\Repositories\AccountingRepository;
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

class ConsolidatedDataController extends Controller
{
    protected $oreRepository;
    protected $supplierRepository;
    protected $dispatchRepository;
    protected $tripRepository;
    protected $vehicleRepository;
    protected $priceRepository;
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
        $data = [];

        if ($roleId == 3 && $jobPositionId == 7) {
            $oresPaginator = $this->oreRepository->getAllOres($request->input('ores_per_page', 10));
            $dispatchesPaginator = $this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10));

            $data['ores'] = $this->transformPaginated($oresPaginator, OreResource::class);
            $data['dispatches'] = $this->transformPaginated($dispatchesPaginator, DispatchResource::class);
        } else if ($jobPositionId == 4) {
            $oresPaginator = $this->oreRepository->getAllOres($request->input('ores_per_page', 10));

            $data['ores'] = $this->transformPaginated($oresPaginator, OreResource::class);
            $data['suppliers'] = SupplierResource::collection($this->supplierRepository->getAllSuppliers());
            $data['oreType'] = OreTypeResource::collection($this->oreTypeRepository->getOreTypes());
            $data['oreQualityType'] = OreQualityTypeResource::collection($this->oreQualityTypeRepository->getAllOreQualityTypes());
            $data['oreQualityGrade'] = OreQualityGradeResource::collection($this->oreQualityGradeRepository->getAllOreQualityGrade());

        } else if ($jobPositionId == 5) {
            $dispatchesPaginator = $this->dispatchRepository->getDispatchesForDriver($userId, $request->input('dispatches_per_page', 10));
            $tripsPaginator = $this->tripRepository->getTripsForDriver($userId, $request->input('trips_per_page', 10));

            $data['dispatches'] = $this->transformPaginated($dispatchesPaginator, DispatchResource::class);
            $data['trips'] = $this->transformPaginated($tripsPaginator, TripResource::class);

        } else if (in_array($roleId, [1, 2, 3])) {
            $data = $this->getComprehensiveData($request);
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
    private function getComprehensiveData(GetConsolidatedDataRequest $request)
    {
        $data = [
            'dispatches' => $this->transformPaginated(
                $this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10)),
                DispatchResource::class
            ),
            'ores' => $this->transformPaginated(
                $this->oreRepository->getAllOres($request->input('ores_per_page', 10)),
                OreResource::class
            ),
            'suppliers' => ['data' => SupplierResource::collection($this->supplierRepository->getAllSuppliers() ?? collect())],

            'trips' => $this->transformPaginated(
                $this->tripRepository->getAllTrips($request->input('trips_per_page', 10)),
                TripResource::class
            ),
            'vehicles' => $this->transformPaginated(
                $this->vehicleRepository->getAllVehicles($request->input('vehicles_per_page', 10)),
                VehicleResource::class
            ),
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

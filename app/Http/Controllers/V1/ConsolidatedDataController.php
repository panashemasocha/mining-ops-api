<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\GetConsolidatedDataRequest;
use App\Http\Resources\CostPriceResource;
use App\Http\Resources\OreCollection;
use App\Http\Resources\UserRoleResource;
use App\Repositories\OreRepository;
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
        RoleRepository $roleRepository
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

        if (in_array($roleId, [1, 2, 3])) {
            if ($roleId == 3 && $jobPositionId == 7) {
                $data['ores'] = $this->formatPaginatedResults(
                    $this->oreRepository->getAllOres($request->input('ores_per_page', 10)),
                    OreResource::class
                );
                $data['dispatches'] = $this->formatPaginatedResults(
                    $this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10)),
                    DispatchResource::class
                );
            } else {
                $data = $this->getComprehensiveData($request);
            }
        } else {
            switch ($jobPositionId) {
                case 4:
                    $data['ores'] = $this->formatPaginatedResults(
                        $this->oreRepository->getAllOres($request->input('ores_per_page', 10)),
                        OreResource::class
                    );
                    $data['suppliers'] = $this->formatPaginatedResults(
                        $this->supplierRepository->getAllSuppliers($request->input('suppliers_per_page', 10)),
                        SupplierResource::class
                    );
                    break;
                case 7:
                    $data['ores'] = $this->formatPaginatedResults(
                        $this->oreRepository->getAllOres($request->input('ores_per_page', 10)),
                        OreResource::class
                    );
                    $data['dispatches'] = $this->formatPaginatedResults(
                        $this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10)),
                        DispatchResource::class
                    );
                    break;
                case 5:
                    $data['dispatches'] = $this->formatPaginatedResults(
                        $this->dispatchRepository->getDispatchesForDriver($userId, $request->input('dispatches_per_page', 10)),
                        DispatchResource::class
                    );
                    $data['trips'] = $this->formatPaginatedResults(
                        $this->tripRepository->getTripsForDriver($userId, $request->input('trips_per_page', 10)),
                        TripResource::class
                    );
                    break;
                default:
                    return response()->json(['error' => 'Unauthorized or invalid job position'], 403);
            }
        }

        return response()->json($data, 200);
    }

    private function getComprehensiveData(GetConsolidatedDataRequest $request)
    {
        return [
            'dispatches' => $this->formatPaginatedResults(
                $this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10)),
                DispatchResource::class
            ),
            'ores' => $this->formatPaginatedResults(
                $this->oreRepository->getAllOres($request->input('ores_per_page', 10)),
                OreResource::class
            ),
            'suppliers' => $this->formatPaginatedResults(
                $this->supplierRepository->getAllSuppliers($request->input('suppliers_per_page', 10)),
                SupplierResource::class
            ),
            'trips' => $this->formatPaginatedResults(
                $this->tripRepository->getAllTrips($request->input('trips_per_page', 10)),
                TripResource::class
            ),
            'vehicles' => $this->formatPaginatedResults(
                $this->vehicleRepository->getAllVehicles($request->input('vehicles_per_page', 10)),
                VehicleResource::class
            ),
            'prices' => CostPriceResource::collection($this->priceRepository->getAllPrices()),
            'departments' => DepartmentResource::collection($this->departmentRepository->getAllDepartments()),
            'branches' => BranchResource::collection($this->branchRepository->getAllBranches()),
            'jobPositions' => JobPositionResource::collection($this->jobPositionRepository->getAllJobPositions()),
            'roles' => UserRoleResource::collection($this->roleRepository->getAllRoles()),
        ];
    }

    /**
     * Format paginated results with data, links, and meta.
     */
    private function formatPaginatedResults($paginator, $resourceClass)
    {
        return [
            'data' => $resourceClass::collection($paginator->items()),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ];
    }

}
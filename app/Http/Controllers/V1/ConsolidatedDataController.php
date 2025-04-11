<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\GetConsolidatedDataRequest;
use App\Models\CostPrice;
use App\Models\UserRole;
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
use App\Http\Resources\PriceResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\BranchResource;
use App\Http\Resources\JobPositionResource;
use App\Http\Resources\RoleResource;

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
        $roleId = $request->role_Id;
        $jobPositionId = $request->job_position_id;
        $userId = $request->id;

        $data = [];

        // Role-based precedence
        if (in_array($roleId, [1, 2, 3])) {
            return response()->json([
                'error' => 'first block',
                "role" => $roleId,
                "job position" => $jobPositionId,
                "user id" => $userId
            ], 403);
            if ($roleId == 3 && $jobPositionId == 7) {
                $data['ores'] = OreResource::collection($this->oreRepository->getAllOres($request->input('ores_per_page', 10)));
                $data['dispatches'] = DispatchResource::collection($this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10)));
            } else {
                $data = $this->getComprehensiveData($request);
            }
        } else {
            return response()->json([
                'error' => '2nd block',
                "role" => $roleId,
                "job position" => $jobPositionId,
                "user id" => $userId
            ], 403);
            // Job position-based data
            switch ($jobPositionId) {
                case 4:
                    $data['ores'] = OreResource::collection($this->oreRepository->getAllOres($request->input('ores_per_page', 10)));
                    $data['suppliers'] = SupplierResource::collection($this->supplierRepository->getAllSuppliers($request->input('suppliers_per_page', 10)));
                    break;
                case 7:
                    $data['ores'] = OreResource::collection($this->oreRepository->getAllOres($request->input('ores_per_page', 10)));
                    $data['dispatches'] = DispatchResource::collection($this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10)));
                    break;
                case 5:
                    $data['dispatches'] = DispatchResource::collection($this->dispatchRepository->getDispatchesForDriver($userId, $request->input('dispatches_per_page', 10)));
                    $data['trips'] = TripResource::collection($this->tripRepository->getTripsForDriver($userId, $request->input('trips_per_page', 10)));
                    break;
                default:
                    return response()->json(['error' => 'Unauthorized or invalid job position'], 403);
            }
        }

        return response()->json($data, 200);
    }

    /**
     * Retrieve comprehensive data for role IDs 1, 2, or 3 (except Site clerk with roleId 3).
     *
     * @param GetConsolidatedDataRequest $request
     * @return array
     */
    private function getComprehensiveData(GetConsolidatedDataRequest $request)
    {
        return [
            'dispatches' => DispatchResource::collection($this->dispatchRepository->getAllDispatches($request->input('dispatches_per_page', 10))),
            'ores' => OreResource::collection($this->oreRepository->getAllOres($request->input('ores_per_page', 10))),
            'suppliers' => SupplierResource::collection($this->supplierRepository->getAllSuppliers($request->input('suppliers_per_page', 10))),
            'trips' => TripResource::collection($this->tripRepository->getAllTrips($request->input('trips_per_page', 10))),
            'vehicles' => VehicleResource::collection($this->vehicleRepository->getAllVehicles($request->input('vehicles_per_page', 10))),
            'prices' => CostPrice::collection($this->priceRepository->getAllPrices()),
            'departments' => DepartmentResource::collection($this->departmentRepository->getAllDepartments()),
            'branches' => BranchResource::collection($this->branchRepository->getAllBranches()),
            'jobPositions' => JobPositionResource::collection($this->jobPositionRepository->getAllJobPositions()),
            'roles' => UserRole::collection($this->roleRepository->getAllRoles()),
        ];
    }
}
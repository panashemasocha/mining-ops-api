<?php

namespace App\Http\Controllers\V1;

use App\Repositories\OreRepository;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConsolidatedData(Request $request)
    {
        // Assuming authenticated user; in practice, validate user data if passed manually
        $user = $request->user(); // Requires auth middleware
        $roleId = $user->roleId;
        $jobPosition = "Driver";
        $userId = $user->id;

        $data = [];

        // Role-based precedence
        if (in_array($roleId, [1, 2, 3])) {
            if ($roleId == 3 && $jobPosition == 'Site clerk') {
                $data['ores'] = $this->oreRepository->getAllOres();
                $data['dispatches'] = $this->dispatchRepository->getAllDispatches();
            } else {
                $data = $this->getComprehensiveData();
            }
        } else {
            // Job position-based data
            switch ($jobPosition) {
                case 'Quality controller':
                    $data['ores'] = $this->oreRepository->getAllOres();
                    $data['suppliers'] = $this->supplierRepository->getAllSuppliers();
                    break;
                case 'Site clerk':
                    $data['ores'] = $this->oreRepository->getAllOres();
                    $data['dispatches'] = $this->dispatchRepository->getAllDispatches();
                    break;
                case 'Driver':
                    $data['dispatches'] = $this->dispatchRepository->getDispatchesForDriver($userId);
                    $data['trips'] = $this->tripRepository->getTripsForDriver($userId);
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
     * @return array
     */
    private function getComprehensiveData()
    {
        return [
            'dispatches' => $this->dispatchRepository->getAllDispatches(),
            'ores' => $this->oreRepository->getAllOres(),
            'suppliers' => $this->supplierRepository->getAllSuppliers(),
            'trips' => $this->tripRepository->getAllTrips(),
            'vehicles' => $this->vehicleRepository->getAllVehicles(),
            'prices' => $this->priceRepository->getAllPrices(),
            'departments' => $this->departmentRepository->getAllDepartments(),
            'branches' => $this->branchRepository->getAllBranches(),
            'jobPositions' => $this->jobPositionRepository->getAllJobPositions(),
            'roles' => $this->roleRepository->getAllRoles(),
        ];
    }
}
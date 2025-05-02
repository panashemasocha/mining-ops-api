<?php

namespace App\Http\Controllers\V1;

use App\Repositories\SupplierRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersStatisticalDataController extends Controller
{
    protected $userRepository;
    protected $supplierRepository;
    public function __construct(UserRepository $userRepository,SupplierRepository $supplierRepository) {
        $this->userRepository = $userRepository;
        $this->supplierRepository = $supplierRepository;
    }
}

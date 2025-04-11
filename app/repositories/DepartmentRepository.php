<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository
{
    public function getAllDepartments()
    {
        return Department::all();
    }
}
<?php

namespace App\Repositories;

use App\Models\UserRole;

class RoleRepository
{
    public function getAllRoles()
    {
        return UserRole::all();
    }
}
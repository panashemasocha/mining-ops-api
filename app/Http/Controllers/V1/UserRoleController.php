<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreUserRoleRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Resources\UserRoleResource;
use App\Models\UserRole;
use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = $request->paging === 'false' ? UserRole::all() : UserRole::paginate(10);
        return UserRoleResource::collection($roles);
    }

    public function store(StoreUserRoleRequest $request)
    {
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']);
        $role = UserRole::create($data);
        return new UserRoleResource($role);
    }

    public function show($id)
    {
        $role = UserRole::findOrFail($id);
        return new UserRoleResource($role);
    }

    public function update(UpdateUserRoleRequest $request, $id)
    {
        $role = UserRole::findOrFail($id);
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']);
        $role->update($data);
        return new UserRoleResource($role);
    }

    public function destroy($id)
    {
        $role = UserRole::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'User role deleted']);
    }
}
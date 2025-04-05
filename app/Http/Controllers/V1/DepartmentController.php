<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = $request->paging === 'false' ? Department::all() : Department::paginate(10);
        return DepartmentResource::collection($departments);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']);
        $department = Department::create($data);
        return new DepartmentResource($department);
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);
        return new DepartmentResource($department);
    }

    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = Department::findOrFail($id);
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']);
        $department->update($data);
        return new DepartmentResource($department);
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
        return response()->json(['message' => 'Department deleted']);
    }
}
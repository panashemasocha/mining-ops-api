<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreJobPositionRequest;
use App\Http\Requests\UpdateJobPositionRequest;
use App\Http\Resources\JobPositionResource;
use App\Models\JobPosition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobPositionController extends Controller
{
    public function index(Request $request)
    {
        $jobPositions = $request->query('paging', 'true') === 'false'
            ? JobPosition::all()
            : JobPosition::paginate(10);
        return JobPositionResource::collection($jobPositions);
    }

    public function store(StoreJobPositionRequest $request)
    {
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']); 
        $jobPosition = JobPosition::create($data);
        return new JobPositionResource($jobPosition);
    }

    public function show($id)
    {
        $jobPosition = JobPosition::findOrFail($id);
        return new JobPositionResource($jobPosition);
    }

    public function update(UpdateJobPositionRequest $request, $id)
    {
        $jobPosition = JobPosition::findOrFail($id);
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']);
        $jobPosition->update($data);
        return new JobPositionResource($jobPosition);
    }

    public function destroy($id)
    {
        $jobPosition = JobPosition::findOrFail($id);
        $jobPosition->delete();
        return response()->json(['message' => 'Job position deleted'], 200);
    }
}
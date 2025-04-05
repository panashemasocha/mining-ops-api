<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = $request->paging === 'false' ? Branch::all() : Branch::paginate(10);
        return BranchResource::collection($branches);
    }

    public function store(StoreBranchRequest $request)
    {
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']);
        $branch = Branch::create($data);
        return new BranchResource($branch);
    }

    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        return new BranchResource($branch);
    }

    public function update(UpdateBranchRequest $request, $id)
    {
        $branch = Branch::findOrFail($id);
        $data = $request->validated();
        $data['name'] = ucfirst($data['name']);
        $branch->update($data);
        return new BranchResource($branch);
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();
        return response()->json(['message' => 'Branch deleted']);
    }
}
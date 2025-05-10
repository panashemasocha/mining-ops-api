<?php
namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = $request->query('paging', 'true') === 'false'
            ? Supplier::all()
            : Supplier::paginate(10);
        return SupplierResource::collection($suppliers);
    }

    public function store(StoreSupplierRequest $request)
    {
        $data = $request->validated();
        $data['first_name'] = ucfirst($data['first_name']);
        $data['last_name'] = ucfirst($data['last_name']);
        $supplier = Supplier::create($data);
        return new SupplierResource($supplier);
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return new SupplierResource($supplier);
    }

    public function update(UpdateSupplierRequest $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $data = $request->validated();
        if (array_key_exists('first_name', $data) && $data['first_name'] !== null) {
            $data['first_name'] = ucfirst($data['first_name']);
        }
        if (array_key_exists('last_name', $data) && $data['last_name'] !== null) {
            $data['last_name'] = ucfirst($data['last_name']);
        }
        $supplier->update($data);
        return new SupplierResource($supplier);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted'], 200);
    }
}
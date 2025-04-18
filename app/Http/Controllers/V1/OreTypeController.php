<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\OreTypeResource;
use App\Models\OreType;
use App\Http\Requests\StoreOreTypeRequest;
use App\Http\Requests\UpdateOreTypeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OreTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $oreTypes = $request->query('paging', 'true') === 'false'
            ? OreType::all()
            : OreType::paginate(10);
        return OreTypeResource::collection($oreTypes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOreTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OreType $oreType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OreType $oreType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOreTypeRequest $request, OreType $oreType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OreType $oreType)
    {
        //
    }
}

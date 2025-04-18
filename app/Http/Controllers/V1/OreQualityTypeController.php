<?php

namespace App\Http\Controllers\V1;

use App\Models\OreQualityType;
use App\Http\Requests\StoreOreQualityTypeRequest;
use App\Http\Requests\UpdateOreQualityTypeRequest;
use Database\Factories\OreQualityTypeFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OreQualityTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $oreQualityType = $request->query('paging', 'true') === 'false'
            ? OreQualityType::all()
            : OreQualityType::paginate(10);
        return OreQualityTypeFactory::collection($oreQualityType);
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
    public function store(StoreOreQualityTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OreQualityType $oreQualityType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OreQualityType $oreQualityType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOreQualityTypeRequest $request, OreQualityType $oreQualityType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OreQualityType $oreQualityType)
    {
        //
    }
}

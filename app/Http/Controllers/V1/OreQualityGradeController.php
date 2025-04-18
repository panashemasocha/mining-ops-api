<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\OreQualityGradeResource;
use App\Models\OreQualityGrade;
use App\Http\Requests\StoreOreQualityGradeRequest;
use App\Http\Requests\UpdateOreQualityGradeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OreQualityGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $oreQualityGrades = $request->query('paging', 'true') === 'false'
            ? OreQualityGrade::all()
            : OreQualityGrade::paginate(10);
        return OreQualityGradeResource::collection($oreQualityGrades);
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
    public function store(StoreOreQualityGradeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OreQualityGrade $oreQualityGrade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OreQualityGrade $oreQualityGrade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOreQualityGradeRequest $request, OreQualityGrade $oreQualityGrade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OreQualityGrade $oreQualityGrade)
    {
        //
    }
}

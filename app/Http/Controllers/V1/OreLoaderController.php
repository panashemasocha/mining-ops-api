<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\OreLoaderResource;
use App\Models\OreLoader;
use App\Http\Requests\StoreOreLoaderRequest;
use App\Http\Requests\UpdateOreLoaderRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OreLoaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $loaders = $request->query('paging', 'true') === 'false'
            ? OreLoader::all()
            : OreLoader::paginate(10);
        return OreLoaderResource::collection($loaders);
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
    public function store(StoreOreLoaderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OreLoader $oreLoader)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OreLoader $oreLoader)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOreLoaderRequest $request, OreLoader $oreLoader)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OreLoader $oreLoader)
    {
        //
    }
}

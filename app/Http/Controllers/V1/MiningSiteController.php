<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\MiningSiteResource;
use App\Models\MiningSite;
use App\Http\Requests\StoreMiningSiteRequest;
use App\Http\Requests\UpdateMiningSiteRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MiningSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sites = $request->query('paging', 'true') === 'false'
        ? MiningSite::all()
        : MiningSite::paginate(10);

    return MiningSiteResource::collection($sites);

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
    public function store(StoreMiningSiteRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MiningSite $miningSite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MiningSite $miningSite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMiningSiteRequest $request, MiningSite $miningSite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MiningSite $miningSite)
    {
        //
    }
}

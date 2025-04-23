<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OreLoaderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'loaders'=>$this->loaders,
            'tripId' => $this->trip_id,
        ];
    }
}
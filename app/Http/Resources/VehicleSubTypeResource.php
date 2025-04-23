<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleSubTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->name,
        ];
    }
}
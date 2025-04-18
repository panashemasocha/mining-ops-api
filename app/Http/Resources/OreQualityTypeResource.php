<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OreQualityTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'quality' => $this->quality,
        ];
    }
}
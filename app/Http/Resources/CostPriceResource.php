<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CostPriceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'commodity' => $this->commodity,
            'oreType' => $this->ore_type,
            'quality' => $this->quality,
            'price' => $this->price,
            'dateCreated' => $this->date_created,
            'createdBy' => $this->created_by,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
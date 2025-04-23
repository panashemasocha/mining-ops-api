<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DieselAllocationTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
        ];
    }
}
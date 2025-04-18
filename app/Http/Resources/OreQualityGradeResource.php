<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OreQualityGradeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'grade' => $this->grade,
        ];
    }
}

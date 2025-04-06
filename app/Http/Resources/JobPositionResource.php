<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobPositionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'roleId' => $this->role_id,
           // 'createdAt' => $this->created_at,
           // 'updatedAt' => $this->updated_at,
        ];
    }
}
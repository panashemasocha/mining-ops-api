<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->account_name,
            'type' => $this->account_type,
            'status' => $this->status == 1 ? 'Active' : 'Inactive',
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
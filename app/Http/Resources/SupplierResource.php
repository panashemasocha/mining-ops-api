<?php
namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'nationalId' => $this->national_id,
            'physicalAddress' => $this->physical_address,
            'paymentMethod' => new PaymentMethodResource($this->paymentMethod),
            'phoneNumber' => $this->phone_number,
            'creator' => new UserResource(User::find($this->created_by)),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
<?php
namespace App\Http\Resources;

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
            'createdBy' => $this->created_by,
            'paymentMethodId' => $this->payment_method_id,
            'phoneNumber' => $this->phone_number,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
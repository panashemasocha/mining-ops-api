<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GlPaymentAllocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'paymentTransaction' => new GLTransactionResource($this->paymentTransaction),
            'invoiceTransaction' => new GLTransactionResource($this->invoiceTransaction),
            'allocatedAmount' => $this->allocated_amount,
            'createdAt' => $this->created_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GLTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'transactionDate'  => $this->trans_date,
            'description'      => $this->description,
            'transType'        => $this->trans_type,
            'supplier'         => new SupplierResource($this->whenLoaded('supplier')),
            'trip'             => new TripResource($this->whenLoaded('trip')),
            'creator'          => new UserResource($this->whenLoaded('creator')),
            'entries'          => GLEntryResource::collection($this->whenLoaded('entries')),
            'allocations'      => GlPaymentAllocationResource::collection($this->whenLoaded($this->trans_type === 'vendor payment' ? 'paymentAllocations' : 'invoiceAllocations')),
            'createdAt'        => $this->created_at,
        ];
    }
}

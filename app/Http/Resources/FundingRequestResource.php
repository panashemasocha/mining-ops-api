<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FundingRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'amount'         => $this->amount,
            'paymentMethod'  => new PaymentMethodResource($this->paymentMethod),
            'account'        => new AccountResource($this->account),
            'purpose'        => $this->purpose,
            'approvalNotes'  => $this->approval_notes,
            'department'     => new DepartmentResource($this->department),
            'miningSite'     => new MiningSiteResource($this->miningSite),
            'accountant'     => new UserResource($this->accountant),
            'approvedBy'     => new UserResource($this->approvedBy),
            'decisionDate'   => $this->decision_date,
            'status'         => $this->status,
            'createdAt'      => $this->created_at,
            'updatedAt'      => $this->updated_at,
        ];
    }
}
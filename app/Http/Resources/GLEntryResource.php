<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GLEntryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'account'    => $this->account->account_name,
            'debitAmount'  => $this->debit_amt,
            'creditAmount' => $this->credit_amt,
        ];
    }
}

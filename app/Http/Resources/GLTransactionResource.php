<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GLTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'transactionDate' => $this->trans_date,
            'description' => $this->description,
            'creator' => new UserResource($this->creator),
            'entries' => GLEntryResource::collection($this->entries),
            'createdAt' => $this->created_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequisitionStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'today' => [
                'pending' => $this['today']['pending'],
                'accepted' => $this['today']['accepted'],
                'rejected' => $this['today']['rejected'],
            ],
            'thisWeek' => [
                'pending' => $this['thisWeek']['pending'],
                'accepted' => $this['thisWeek']['accepted'],
                'rejected' => $this['thisWeek']['rejected'],
            ],
            'thisMonth' => [
                'pending' => $this['thisMonth']['pending'],
                'accepted' => $this['thisMonth']['accepted'],
                'rejected' => $this['thisMonth']['rejected'],
            ],
            'overall' => [
                'pending' => $this['overall']['pending'],
                'accepted' => $this['overall']['accepted'],
                'rejected' => $this['overall']['rejected'],
            ],
        ];
    }
}
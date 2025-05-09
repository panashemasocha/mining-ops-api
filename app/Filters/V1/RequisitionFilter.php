<?php

namespace App\Filters\V1;

use App\Filters\AbstractApiFilter;

class RequisitionFilter extends AbstractApiFilter
{
    /**
     * Parameters that can be filtered and their allowed operators.
     */
    protected $safeParams = [
        'status' => ['eq'],
        'accountId' => ['eq'],
    ];
    
    /**
     * Map request parameters to database columns.
     */
    protected $columnMap = [
        'status' => 'status',
        'accountId' => 'account_id'
    ];
}
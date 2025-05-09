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

    /**
     * List of parameters that should skip filtering when their value is 'any'.
     * Override the parent property to customize for this specific filter.
     */
    protected $skipWhenAny = ['status'];
}
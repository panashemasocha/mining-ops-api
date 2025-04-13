<?php

namespace App\Repositories;

use App\Models\GLTransaction;

class AccountingRepository
{
    public function getAllFinancials($perPage = 10)
    {
        return GLTransaction::paginate($perPage, ['*'], 'ores_page');
    }
}
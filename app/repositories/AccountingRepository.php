<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use Log;

class AccountingRepository
{
    public function getAllFinancials($perPage = 10)
    {
        return GLTransaction::paginate($perPage, ['*'], 'financials_page');
    }

    /**
     * Get cashbook totals for “Cash on Hand”.
     *
     * @param  string|null  $startDate  YYYY‑MM‑DD
     * @param  string|null  $endDate    YYYY‑MM‑DD
     * @return array
     *
     * @throws \RuntimeException if the Cash on Hand account is missing
     */
    public function getCashbookTotals(?string $startDate, ?string $endDate): array
    {
        $cashAccount = Account::where('account_name', 'Cash on Hand')->first();

        if (! $cashAccount) {
            Log::error('Cash on Hand account not found.');
            throw new \RuntimeException('Cash on Hand account not found.');
        }

        $query = GLEntry::where('account_id', $cashAccount->id);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $receipts = $query->sum('debit_amt');
        $payments = $query->sum('credit_amt');

        return [
            'cashReceipts' => $receipts,
            'cashPayments' => $payments,
            'balance'      => $receipts - $payments,
        ];
    }
}
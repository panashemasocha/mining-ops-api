<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use App\Models\GlPaymentAllocation;
use Carbon\Carbon;

class AccountingRepository
{
    /**
     * Get the total balance of current assets as of a given date.
     *
     * @param Carbon $asOfDate
     * @return float
     */
    public function getCurrentAssetsBalance($asOfDate)
    {
        $balance = GLEntry::whereIn('account_id', [1, 2, 3])
            ->whereHas('transaction', function ($q) use ($asOfDate) {
                $q->where('trans_date', '<=', $asOfDate);
            })
            ->selectRaw('SUM(debit_amt) - SUM(credit_amt) as balance')
            ->value('balance');

        return $balance ?? 0;
    }

    /**
     * Get the balance of creditors as of a given date.
     *
     * @param Carbon $asOfDate
     * @return float
     */
    public function getCreditorsBalance($asOfDate)
    {
        $balance = GLEntry::where('account_id', 7)
            ->whereHas('transaction', function ($q) use ($asOfDate) {
                $q->where('trans_date', '<=', $asOfDate);
            })
            ->selectRaw('SUM(credit_amt) - SUM(debit_amt) as balance')
            ->value('balance');

        return $balance ?? 0;
    }

    /**
     * Get the total paid expenses within a given period.
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function getTotalPaidExpenses($startDate, $endDate)
    {
        $expenseInvoiceIds = GLTransaction::whereHas('entries', function ($q) {
            $q->whereIn('account_id', [4, 5, 6])->where('debit_amt', '>', 0);
        })->pluck('id');

        $totalPaid = GlPaymentAllocation::whereIn('invoice_trans_id', $expenseInvoiceIds)
            ->whereHas('paymentTransaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('trans_date', [$startDate, $endDate]);
            })
            ->sum('allocated_amount');

        return $totalPaid ?? 0;
    }

}
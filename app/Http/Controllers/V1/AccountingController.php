<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViewCashbookRequest;
use App\Models\Account;
use App\Models\GLEntry;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function cashbook(ViewCashbookRequest $request)
    {
        $cashAccount = Account::where('account_name', 'Cash on Hand')->first();

        if (!$cashAccount) {
            return response()->json([
                'message' => 'Cash on Hand account not found.'
            ], 404);
        }

        $query = GLEntry::where('account_id', $cashAccount->id);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $receipts = (clone $query)->sum('debit_amt');
        $payments = (clone $query)->sum('credit_amt');
        $balance = $receipts - $payments;

        return response()->json([
            'cashReceipts' => $receipts,
            'cashPayments' => $payments,
            'balance' => $balance,
        ]);
    }

    /**
     * Return all accounts with their current balance.
     */
    public function accountsWithBalances(Request $request)
    {
        $accounts = Account::withSum('entries as total_debits', 'debit_amt')
            ->withSum('entries as total_credits', 'credit_amt')
            ->get()
            ->map(function (Account $account) {
                $debits = (float) $account->total_debits;
                $credits = (float) $account->total_credits;

                // Assets & Expenses carry a debit balance;
                // Liabilities, Equity & Revenue carry a credit balance.
                if (in_array($account->account_type, ['Asset', 'Expense'], true)) {
                    $balance = $debits - $credits;
                } else {
                    $balance = $credits - $debits;
                }

                return [
                    'id' => $account->id,
                    'accountName' => $account->account_name,
                    'accountType' => $account->account_type,
                    'status' => $account->status === 1 ? 'Active' : 'Inactive',
                    'balance' => number_format($balance, 2, '.', ''),
                    'createdAt' => $account->created_at->toDateTimeString(),
                    'updatedAt' => $account->updated_at->toDateTimeString(),
                ];
            });

        return response()->json($accounts);
    }


}

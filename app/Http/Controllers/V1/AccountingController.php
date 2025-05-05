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

     /**
     * GET  /api/accounts/{id}
     * Show an account’s current balance and paginated transaction statement
     * with running balances across pages.
     */
    public function accountTransactions(Request $request, int $id)
    {
        // 1) Load account & compute natural current balance
        $account = Account::withSum('entries as total_debits', 'debit_amt')
                          ->withSum('entries as total_credits', 'credit_amt')
                          ->findOrFail($id);

        $debits  = (float) $account->total_debits;
        $credits = (float) $account->total_credits;
        $isDebitNatural = in_array($account->account_type, ['Asset', 'Expense'], true);

        $currentBalance = $isDebitNatural
            ? $debits - $credits
            : $credits - $debits;

        // 2) Setup pagination parameters
        $perPage = (int) $request->query('per_page', 15);
        $page    = (int) $request->query('page', 1);
        $offset  = ($page - 1) * $perPage;

        // 3) Compute opening balance = sum of amounts of all entries before this page
        $openingBalance = 0.0;
        if ($offset > 0) {
            $priorEntries = GLEntry::with('transaction')
                ->where('account_id', $id)
                ->orderBy('trans_date','desc')
                ->orderBy('id')
                ->skip(0)
                ->take($offset)
                ->get();

            foreach ($priorEntries as $entry) {
                $amt = $isDebitNatural
                    ? $entry->debit_amt - $entry->credit_amt
                    : $entry->credit_amt - $entry->debit_amt;

                $openingBalance += $amt;
            }
        }

        // 4) Fetch & transform current page entries, carrying forward running balance
        $running = $openingBalance;
        $paginated = GLEntry::with('transaction')
            ->where('account_id', $id)
            ->orderBy('trans_date','desc')
            ->orderBy('id')
            ->paginate($perPage)
            ->through(function (GLEntry $entry) use (&$running, $isDebitNatural) {
                $txn = $entry->transaction;
                $amt = $isDebitNatural
                    ? $entry->debit_amt - $entry->credit_amt
                    : $entry->credit_amt - $entry->debit_amt;

                $running += $amt;

                return [
                    'transactionId'  => $txn->id,
                    'date'           => $txn->trans_date->toDateString(),
                    'type'           => $txn->trans_type,
                    'description'    => $txn->description,
                    'debit'          => number_format($entry->debit_amt,   2, '.', ''),
                    'credit'         => number_format($entry->credit_amt,  2, '.', ''),
                    'amount'         => number_format($amt,               2, '.', ''),
                    'runningBalance' => number_format($running,           2, '.', ''),
                ];
            });

        // 5) Return JSON with account header + paginated statement
        return response()->json([
            'account' => [
                'id'             => $account->id,
                'name'           => $account->account_name,
                'type'           => $account->account_type,
                'status'         => $account->status === 1 ? 'Active' : 'Inactive',
                'currentBalance' => number_format($currentBalance, 2, '.', ''),
                'asOfDate'       => now()->toDateTimeString(),
            ],
            'statement' => $paginated,
        ]);
    }


}

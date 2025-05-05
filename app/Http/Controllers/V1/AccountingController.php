<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViewAccountsRequest;
use App\Http\Requests\ViewCashbookRequest;
use App\Models\Account;
use App\Models\GLEntry;
use App\Models\GlPaymentAllocation;
use App\Models\GLTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
     * Show an account’s current balance and paginated transaction statement
     * with running balances across pages.
     */
    public function accountTransactions(Request $request, int $id)
    {
        // 1) Load account & compute natural current balance
        $account = Account::withSum('entries as total_debits', 'debit_amt')
            ->withSum('entries as total_credits', 'credit_amt')
            ->findOrFail($id);

        $debits = (float) $account->total_debits;
        $credits = (float) $account->total_credits;
        $isDebitNatural = in_array($account->account_type, ['Asset', 'Expense'], true);

        $currentBalance = $isDebitNatural
            ? $debits - $credits
            : $credits - $debits;

        // 2) Setup pagination parameters
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $offset = ($page - 1) * $perPage;

        // 3) Compute opening balance = sum of amounts of all entries before this page
        $openingBalance = 0.0;
        if ($offset > 0) {
            $priorEntries = GLEntry::with('transaction')
                ->where('account_id', $id)
                ->orderBy('created_at', 'desc')
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
            ->orderBy('created_at', 'desc')
            ->orderBy('id')
            ->paginate($perPage)
            ->through(function (GLEntry $entry) use (&$running, $isDebitNatural) {
                $txn = $entry->transaction;
                $amt = $isDebitNatural
                    ? $entry->debit_amt - $entry->credit_amt
                    : $entry->credit_amt - $entry->debit_amt;

                $running += $amt;

                return [
                    'transactionId' => $txn->id,
                    'date' => $txn->trans_date->toDateTimeString(),
                    'type' => $txn->trans_type,
                    'description' => $txn->description,
                    'debit' => number_format($entry->debit_amt, 2, '.', ''),
                    'credit' => number_format($entry->credit_amt, 2, '.', ''),
                    'amount' => number_format($amt, 2, '.', ''),
                    'runningBalance' => number_format($running, 2, '.', ''),
                ];
            });

        // 5) Return JSON with account header + paginated statement
        return response()->json([
            'account' => [
                'id' => $account->id,
                'name' => $account->account_name,
                'type' => $account->account_type,
                'status' => $account->status === 1 ? 'Active' : 'Inactive',
                'currentBalance' => number_format($currentBalance, 2, '.', ''),
                'asOfDate' => now()->toDateTimeString(),
            ],
            'statement' => $paginated,
        ]);
    }

    /**
     * GET  /api/accounts/invoices
     * Invoice summary & list for period.
     */
    public function invoiceReport(ViewAccountsRequest $request)
    {
        // 1) Determine period
        $start = $request->input('startDate')
            ? Carbon::parse($request->input('startDate'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $end = $request->input('endDate')
            ? Carbon::parse($request->input('endDate'))->endOfDay()
            : Carbon::now();

        // 2) Base invoice transaction IDs in period
        $invoiceTxnIds = GLTransaction::where('trans_type', 'invoice')
            ->whereBetween('trans_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('id');

        // 3) Helper to compute totals/unpaid for a given cost‐account
        $makeStats = function (int $acctId) use ($invoiceTxnIds) {
            // total debited to expense
            $total = GLEntry::whereIn('trans_id', $invoiceTxnIds)
                ->where('account_id', $acctId)
                ->sum('debit_amt');
            // paid against those invoices (allocations)
            $paid = GlPaymentAllocation::whereIn('invoice_trans_id', $invoiceTxnIds)
                ->sum('allocated_amount');
            return [
                'totalAmount' => number_format($total, 2, '.', ''),
                'unpaidAmount' => number_format($total - $paid, 2, '.', ''),
            ];
        };

        // 4) Paginate the invoice transactions with their entries
        $invoices = GLTransaction::with('entries')
            ->whereIn('id', $invoiceTxnIds)
            ->orderBy('trans_date', 'desc')
            ->paginate(15);

        return response()->json([
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'ore' => $makeStats(4),
            'diesel' => $makeStats(5),
            'loadingCost' => $makeStats(6),
            'invoices' => $invoices,
        ]);
    }

    /**
     * GET  /api/accounts/payments
     * Payment summary & list for period.
     */
    public function paymentReport(ViewAccountsRequest $request)
    {
        // 1) Determine period
        $start = $request->input('startDate')
            ? Carbon::parse($request->input('startDate'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $end = $request->input('endDate')
            ? Carbon::parse($request->input('endDate'))->endOfDay()
            : Carbon::now();

        // Invoice txns in period (for stats)
        $invoiceTxnIds = GLTransaction::where('trans_type', 'invoice')
            ->whereBetween('trans_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('id');

        // Payment txns in period
        $paymentTxns = GLTransaction::with('entries')
            ->where('trans_type', 'payment')
            ->whereBetween('trans_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('trans_date', 'desc')
            ->paginate(15);

        // 2) Helper for invoiced/paid/unpaid per cost‐account
        $makeStats = function (int $acctId) use ($invoiceTxnIds) {
            $invoiced = GLEntry::whereIn('trans_id', $invoiceTxnIds)
                ->where('account_id', $acctId)
                ->sum('debit_amt');
            $paid = GlPaymentAllocation::whereIn('invoice_trans_id', $invoiceTxnIds)
                ->sum('allocated_amount');
            return [
                'totalInvoicedAmount' => number_format($invoiced, 2, '.', ''),
                'paidAmount' => number_format($paid, 2, '.', ''),
                'unpaidAmount' => number_format($invoiced - $paid, 2, '.', ''),
            ];
        };

        // 3) Count partially‐paid invoices in period
        $partialCount = collect($invoiceTxnIds)
            ->filter(function ($invId) {
                $total = GLEntry::where('trans_id', $invId)->sum('debit_amt');
                $paidAmt = GlPaymentAllocation::where('invoice_trans_id', $invId)
                    ->sum('allocated_amount');
                return $paidAmt > 0 && $paidAmt < $total;
            })->count();

        return response()->json([
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'ore' => $makeStats(4),
            'diesel' => $makeStats(5),
            'loadingCost' => $makeStats(6),
            'partiallyPaidInvoices' => $partialCount,
            'payments' => $paymentTxns,
        ]);
    }
}




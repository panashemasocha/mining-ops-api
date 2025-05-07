<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceiptRequest;
use App\Http\Requests\ViewAccountsRequest;
use App\Http\Requests\ViewCashbookRequest;
use App\Models\Account;
use App\Models\GLEntry;
use App\Models\GlPaymentAllocation;
use App\Models\GLTransaction;
use DB;
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
        $perPage = $request->get('per_page', 10);

        // paginated query with sums
        $paginated = Account::withSum('entries as total_debits', 'debit_amt')
            ->withSum('entries as total_credits', 'credit_amt')
            ->paginate($perPage);

        // Transform each item in the current page
        $paginated->getCollection()->transform(function (Account $account) {
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
                'name' => $account->account_name,
                'type' => $account->account_type,
                'status' => $account->status === 1 ? 'Active' : 'Inactive',
                'currentBalance' => number_format($balance, 2, '.', ''),
                'createdAt' => $account->created_at->toDateTimeString(),
                'updatedAt' => $account->updated_at->toDateTimeString(),
                'asOfDate' => now()->toDateTimeString(),
            ];
        });

        return response()->json($paginated);
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
                    'trip' => $txn->trip,
                    'supplier' => $txn->supplier,
                    'description' => $txn->description,
                    'debit' => number_format($entry->debit_amt, 2, '.', ''),
                    'credit' => number_format($entry->credit_amt, 2, '.', ''),
                    'amount' => number_format($amt, 2, '.', ''),
                    'runningBalance' => number_format($running, 2, '.', ''),
                    'createdAt' => $txn->created_at,
                    'updatedAt' => $txn->updated_at
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
     * GET  invoices
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

        // Pre-load all invoice IDs in period
        $allInvoiceIds = GLTransaction::where('trans_type', 'invoice')
            ->whereBetween('trans_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('id');

        // 2) Stats per cost‐account 
        $stats = collect([4 => 'ore', 5 => 'diesel', 6 => 'loadingCost'])
            ->mapWithKeys(function ($label, $acctId) use ($allInvoiceIds) {
                // Filter to only those invoices which hit this expense account
                $acctInvoiceIds = GLEntry::whereIn('trans_id', $allInvoiceIds)
                    ->where('account_id', $acctId)
                    ->pluck('trans_id')
                    ->unique();

                // Total invoiced to this expense
                $total = GLEntry::whereIn('trans_id', $acctInvoiceIds)
                    ->where('account_id', $acctId)
                    ->sum('debit_amt');

                // Payments applied to those same invoices
                $paid = GlPaymentAllocation::whereIn('invoice_trans_id', $acctInvoiceIds)
                    ->sum('allocated_amount');

                return [
                    $label => [
                        'totalAmount' => number_format($total, 2, '.', ''),
                        'unpaidAmount' => number_format($total - $paid, 2, '.', ''),
                    ]
                ];
            });

        // 3) Grand totals
        $totalInvoicedAmount = $stats->sum(fn($s) => (float) $s['totalAmount']);
        $totalUnpaidAmount = $stats->sum(fn($s) => (float) $s['unpaidAmount']);

        // 4) Paginate entries via a join so we can order by trans_date
        $perPage = (int) $request->query('per_page', 15);
        $running = 0;

        $entriesQ = GLEntry::select('gl_entries.*')
            ->join('gl_transactions as t', 'gl_entries.trans_id', '=', 't.id')
            ->where('t.trans_type', 'invoice')
            ->whereBetween('t.trans_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('gl_entries.account_id', [4, 5, 6])
            ->orderBy('t.trans_date', 'desc')
            ->orderBy('gl_entries.id');

        $invoices = $entriesQ->paginate($perPage)
            ->through(function (GLEntry $e) use (&$running) {
                $txn = $e->transaction;
                $amt = $e->debit_amt - $e->credit_amt;
                $running += $amt;
                return [
                    'transactionId' => $txn->id,
                    'date' => $txn->trans_date->toDateString(),
                    'type' => $txn->trans_type,
                    'trip' => $txn->trip,
                    'supplier' => $txn->supplier,
                    'description' => $txn->description,
                    'debit' => number_format($e->debit_amt, 2, '.', ''),
                    'credit' => number_format($e->credit_amt, 2, '.', ''),
                    'amount' => number_format($amt, 2, '.', ''),
                    'runningBalance' => number_format($running, 2, '.', ''),
                    'createdAt' => $txn->created_at,
                    'updatedAt' => $txn->updated_at,
                ];
            });

        return response()->json([
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'ore' => $stats['ore'],
            'diesel' => $stats['diesel'],
            'loadingCost' => $stats['loadingCost'],
            'totalInvoicedAmount' => number_format($totalInvoicedAmount, 2, '.', ''),
            'totalUnpaidAmount' => number_format($totalUnpaidAmount, 2, '.', ''),
            'invoices' => $invoices,
        ]);
    }

    /**
     * GET payments
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

        // Pre-load all invoice IDs in period
        $allInvoiceIds = GLTransaction::where('trans_type', 'invoice')
            ->whereBetween('trans_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('id');

        // 2) Stats per cost‐account
        $stats = collect([4 => 'ore', 5 => 'diesel', 6 => 'loadingCost'])
            ->mapWithKeys(function ($acctId, $label) use ($allInvoiceIds) {
                $acctInvoiceIds = GLEntry::whereIn('trans_id', $allInvoiceIds)
                    ->where('account_id', $label)
                    ->pluck('trans_id')
                    ->unique();

                $invoiced = GLEntry::whereIn('trans_id', $acctInvoiceIds)
                    ->where('account_id', $label)
                    ->sum('debit_amt');
                $paid = GlPaymentAllocation::whereIn('invoice_trans_id', $acctInvoiceIds)
                    ->sum('allocated_amount');

                return [
                    $label => [
                        'totalInvoicedAmount' => number_format($invoiced, 2, '.', ''),
                        'paidAmount' => number_format($paid, 2, '.', ''),
                        'unpaidAmount' => number_format($invoiced - $paid, 2, '.', ''),
                    ]
                ];
            });

        // 3) Grand totals
        $totalInvoiced = $stats->sum(function ($s) {
            return (float) $s['totalInvoicedAmount'];
        });
        $totalPaid = $stats->sum(function ($s) {
            return (float) $s['paidAmount'];
        });
        $totalUnpaid = $stats->sum(function ($s) {
            return (float) $s['unpaidAmount'];
        });

        // 4) Paginate entries via join
        $perPage = (int) $request->query('per_page', 15);
        $running = 0;
        $entriesQ = GLEntry::select('gl_entries.*')
            ->join('gl_transactions as t', 'gl_entries.trans_id', '=', 't.id')
            ->where('t.trans_type', 'payment')
            ->whereBetween('t.trans_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('gl_entries.account_id', [4, 5, 6])
            ->orderBy('t.trans_date', 'desc')
            ->orderBy('gl_entries.id');

        // 5) Count partially‐paid invoices
        $partialCount = collect($allInvoiceIds)
            ->filter(function ($invId) {
                $total = GLEntry::where('trans_id', $invId)->sum('debit_amt');
                $paidAmt = GlPaymentAllocation::where('invoice_trans_id', $invId)
                    ->sum('allocated_amount');
                return $paidAmt > 0 && $paidAmt < $total;
            })->count();

        $payments = $entriesQ->paginate($perPage)
            ->through(function (GLEntry $e) use (&$running) {
                $txn = $e->transaction;
                $amt = $e->credit_amt - $e->debit_amt;
                $running += $amt;
                return [
                    'transactionId' => $txn->id,
                    'date' => $txn->trans_date->toDateString(),
                    'type' => $txn->trans_type,
                    'trip' => $txn->trip,
                    'supplier' => $txn->supplier,
                    'description' => $txn->description,
                    'debit' => number_format($e->debit_amt, 2, '.', ''),
                    'credit' => number_format($e->credit_amt, 2, '.', ''),
                    'amount' => number_format($amt, 2, '.', ''),
                    'runningBalance' => number_format($running, 2, '.', ''),
                    'createdAt' => $txn->created_at,
                    'updatedAt' => $txn->updated_at,
                ];
            });

        return response()->json([
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'ore' => $stats['ore'],
            'diesel' => $stats['diesel'],
            'loadingCost' => $stats['loadingCost'],
            'totalInvoicedAmount' => number_format($totalInvoiced, 2, '.', ''),
            'totalPaidAmount' => number_format($totalPaid, 2, '.', ''),
            'totalUnpaidAmount' => number_format($totalUnpaid, 2, '.', ''),
            'partiallyPaidInvoices' => $partialCount,
            'payments' => $payments,
        ]);
    }


    /**
     * Record a payment against a purchase-invoice 
     */
    public function storeReceipt(StoreReceiptRequest $request)
    {
        // 1) Load invoice & selected paying account
        $invoice = GLTransaction::findOrFail($request->invoice_id);
        $assetAcct = Account::findOrFail($request->account_id);
        $amount = $request->amount;
        $payDate = $request->input('payment_date', Carbon::today()->toDateString());

        // 2)Seek AP liability account
        $apAccount = Account::where('id', 7)->firstOrFail();

        // 3) Compute invoice unpaid balance
        $totalInv = $invoice->entries()
            ->where('account_id', $apAccount->id)
            ->value('credit_amt');
        $alreadyPaid = GlPaymentAllocation::where('invoice_trans_id', $invoice->id)
            ->sum('allocated_amount');

        $unpaid = $totalInv - $alreadyPaid;
        if ($amount > $unpaid) {
            return response()->json([
                'message' => "Cannot pay more than unpaid balance ({$unpaid})"
            ], 422);
        }

        // 4) Check Asset account has enough funds (debit balance)
        $assetBalance = $assetAcct->entries()
            ->selectRaw('SUM(debit_amt) - SUM(credit_amt) as bal')
            ->value('bal');

        if ($amount > $assetBalance) {
            return response()->json([
                'message' => "Insufficient funds in {$assetAcct->account_name} ({$assetBalance})"
            ], 422);
        }

        // 5) log in transactions
        DB::transaction(function () use ($invoice, $amount, $payDate, $assetAcct, $apAccount) {
            $isFull = abs($amount - ($invoice->entries()
                ->where('account_id', $apAccount->id)
                ->sum('credit_amt')
                - GlPaymentAllocation::where('invoice_trans_id', $invoice->id)
                    ->sum('allocated_amount'))) < 0.01;

            $desc = $isFull
                ? "Full payment of Invoice #{$invoice->id}"
                : "Partial payment of Invoice #{$invoice->id}";

            // a) create payment transaction
            $payTxn = GLTransaction::create([
                'trans_date' => $payDate,
                'description' => $desc,
                'created_by' => auth()->id(),
                'supplier_id' => $invoice->supplier_id,
                'trans_type' => 'payment',
            ]);

            // b) journal entries: debit Account Payables, credit Asset
            GLEntry::insert([
                [
                    'trans_id' => $payTxn->id,
                    'account_id' => $apAccount->id,
                    'debit_amt' => $amount,
                    'credit_amt' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'trans_id' => $payTxn->id,
                    'account_id' => $assetAcct->id,
                    'debit_amt' => 0,
                    'credit_amt' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // c) link payment to invoice
            GlPaymentAllocation::create([
                'payment_trans_id' => $payTxn->id,
                'invoice_trans_id' => $invoice->id,
                'allocated_amount' => $amount,
            ]);
        });

        return response()->json(['message' => 'Payment recorded'], 201);
    }
}




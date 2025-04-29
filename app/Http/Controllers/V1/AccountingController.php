<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViewCashbookRequest;
use App\Models\Account;
use App\Models\GLEntry;

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
            'balance'       => $balance,
        ]);
    }

    
}

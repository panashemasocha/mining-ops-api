<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Resources\GLTransactionResource;
use App\Models\GLTransaction;
use App\Models\GLEntry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', GLTransaction::class);
        $txs = GLTransaction::with('entries.account','creator')
               ->orderBy('trans_date','desc')
               ->paginate(10);
        return GLTransactionResource::collection($txs);
    }

    public function show($id)
    {
        $this->authorize('view', GLTransaction::class);
        $tx = GLTransaction::with('entries.account','creator')->findOrFail($id);
        return new GLTransactionResource($tx);
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $tx = GLTransaction::create([
            'trans_date'  => $data['trans_date'],
            'description' => $data['description'],
            'created_by'  => auth()->id(),
        ]);
        foreach ($data['entries'] as $entry) {
            GLEntry::create([
                'trans_id'   => $tx->id,
                'account_id' => $entry['account_id'],
                'debit_amt'  => $entry['debit_amt'] ?? 0,
                'credit_amt' => $entry['credit_amt'] ?? 0,
            ]);
        }
        return new GLTransactionResource($tx->load('entries.account','creator'));
    }
}

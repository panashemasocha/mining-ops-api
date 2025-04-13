<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GLEntrySeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch asset and expense accounts
        $assetAccounts = Account::where('account_type', 'Asset')->get();
        $expenseAccounts = Account::where('account_type', 'Expense')->get();

        // For each GL transaction, create a balanced pair of entries
        GLTransaction::all()->each(function (GLTransaction $tx) use ($assetAccounts, $expenseAccounts) {
            // pick random amount between 100 and 1000
            $amount = rand(100, 1000);

            // pick random asset & expense account
            $assetAccount = $assetAccounts->random();
            $expenseAccount = $expenseAccounts->random();

            // Debit expense
            GLEntry::create([
                'trans_id' => $tx->id,
                'account_id' => $expenseAccount->id,
                'debit_amt' => $amount,
                'credit_amt' => 0,
            ]);

            // Credit asset
            GLEntry::create([
                'trans_id' => $tx->id,
                'account_id' => $assetAccount->id,
                'debit_amt' => 0,
                'credit_amt' => $amount,
            ]);
        });
    }

}

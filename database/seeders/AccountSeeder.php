<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $accounts = [
            ['Cash on Hand', 'Asset', 1],
            ['Bank', 'Asset', 0],
            ['Ecocash', 'Asset', 0],
            ['Ore expense', 'Expense', 1],
            ['Diesel expense', 'Expense', 1],
            ['Ore loading expense', 'Expense', 1],
            ['Accounts payable', 'Liability', 1],
            ['Equity', 'Equity', 1]
        ];

        foreach ($accounts as [$name, $type]) {
            Account::firstOrCreate([
                'account_name' => $name,
            ], [
                'account_type' => $type,
            ]);
        }
    }
}

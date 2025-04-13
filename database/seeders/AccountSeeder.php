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
            ['Cash on Hand', 'Asset'],
            ['Bank', 'Asset'],
            ['Ecocash', 'Asset'],
            ['Mining expenses', 'Expense'],
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

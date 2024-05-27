<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee_id = User::select('id')->first();
        $user_ids = User::select('id')
            ->whereNot('id', $employee_id)
            ->get();

        Transaction::factory()
            ->count(240)
            ->sequence(function () use ($employee_id, $user_ids) {
                $index = rand(0, $user_ids->count() - 1);
                $user_id = $user_ids[$index];
                return [
                    'user_id' => $user_id,
                    'employee_id' => $employee_id
                ];
            })
            ->create();
    }
}


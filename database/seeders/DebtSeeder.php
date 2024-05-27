<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DebtSeeder extends Seeder
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

        Debt::factory()
            ->count(12)
            ->sequence(function (Sequence $sequence) use ($user_ids) {
                return [
                    'user_id' => $user_ids[$sequence->index],
                ];
            })
            ->create();
    }
}

<?php

namespace Database\Seeders;

use App\Enums\DebtType;
use App\Enums\PaymentStatus;
use App\Models\Debt;
use App\Models\Installment;
use App\Models\User;
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
            ->afterCreating(function (Debt $debt) {
                $installment = new Installment();
                $installment->debt_id = $debt->id;
                $installment->status = PaymentStatus::UNPAID;
                $installment->amount = rand(50_000, 1_000_000);
                if ($debt->debt_type == DebtType::FLEXIBLE) {
                    $installment->save();
                }
            })
            ->create();
    }
}

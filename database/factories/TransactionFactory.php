<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $txn_types = [TransactionType::NEW_DEBT, TransactionType::PAY_DEBT];
        $txn_type = $this->faker->randomElement($txn_types);
        $installment_number = null;
        $total_installments = null;
        if ($txn_type == TransactionType::PAY_DEBT) {
            $installment_number = $this->faker->numberBetween(1, 12);
        } else {
            $total_installments = rand(1, 12);
        }

        return [
            'transaction_type' => $txn_type,
            'transaction_date' => $this->faker->dateTimeBetween('-4 months'),
            'transaction_amount' => $this->faker->numberBetween(10_000, 1_000_000),
            'installment_number' => $installment_number,
            'total_installments' => $total_installments,
            'description' => $this->faker->paragraph(2)
        ];
    }
}

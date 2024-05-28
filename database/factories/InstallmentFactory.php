<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Installment>
 */
class InstallmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'due_date' => now(),
            'amount' => rand(50_000, 1_000_000),
            'status' => PaymentStatus::UNPAID
        ];
    }
}

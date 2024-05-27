<?php

namespace Database\Factories;

use App\Enums\DebtType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $debt_types = array(DebtType::FLEXIBLE, DebtType::SEASON, DebtType::MONTHLY);
        $debt_type = $this->faker->randomElement($debt_types);

        return [
            'debt_type' => $debt_type,
        ];
    }
}

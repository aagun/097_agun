<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(25)
            ->sequence(fn (Sequence $sequence) => [
                'role_id' => $sequence->index == 0 ? 1 : 2
            ])
            ->create();
    }
}

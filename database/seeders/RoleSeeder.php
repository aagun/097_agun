<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = array('User', 'Admin');

        Role::factory()
            ->count(2)
            ->sequence(fn (Sequence $sequence) => [
                'id' => $sequence->index + 1,
                'name' => 'RO_' . strtoupper($roles[$sequence->index == 0 ? 0 : 1]),
                'description' => $roles[$sequence->index == 0 ? 0 : 1]
            ])
            ->create();
    }
}

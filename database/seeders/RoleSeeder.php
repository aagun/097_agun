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
            ->count(25)
            ->sequence(function (Sequence $sequence) use ($roles) {
                $role = null;

                if ($sequence->index < 2) {
                    $role = $roles[ $sequence->index == 0 ? 0 : 1 ];
                } else {
                    $role = fake()->text(15);
                }

                return [
                    'id' => $sequence->index + 1,
                    'name' => 'RO_' . convertString($role),
                    'description' => $role
                ];
            })
            ->create();
    }
}

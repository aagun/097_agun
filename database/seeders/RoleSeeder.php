<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'id' => '2',
                'name' => 'RO_ADMIN',
                'description' => 'Administrator',
            ],
            [
                'id' => '1',
                'name' => 'RO_USER',
                'description' => 'User',
            ]
        ]);
    }
}

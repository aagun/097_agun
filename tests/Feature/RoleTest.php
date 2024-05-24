<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        DB::delete("delete from roles");
    }

    public function testSeeder()
    {
        $this->seed(RoleSeeder::class);

        $roles = Role::all();

        self::assertCount(2, $roles);
    }

    public function testInsert()
    {

        $role = new Role();
        $role->name = 'RO_TEST';
        $role->description = 'Tester';

        $is_inserted = $role->save();

        self::assertNotNull($role->id);
        self::assertTrue($is_inserted);
        self::assertCount(1, Role::all());

    }

    public function testInsertMany()
    {
        $roles = [
            [
                'name' => 'RO_TEST',
                'description' => 'Tester'],
            [
                'name' => 'RO_SAMPLE',
                'description' => 'Sample'
            ]
        ];

        Role::query()->insert($roles);

        self::assertCount(2, Role::all());
        self::assertEqualsCanonicalizing(count($roles), Role::all()->count());

    }

    public function testSoftDelete()
    {
        $this->seed(RoleSeeder::class);

        $role = Role::find(2); // role admin
        $role->delete();

        self::assertTrue($role->trashed());
    }


}

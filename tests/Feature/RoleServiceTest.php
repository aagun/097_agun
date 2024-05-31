<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Services\RoleService;

class RoleServiceTest extends TestCase
{
    private RoleService $roleService;

    public function testDeleteIdIn()
    {
        // Arrange
        $this->seed(RoleSeeder::class);

        $listIdRole = [1, 2, 3, 4, 5];

        // Action
        $this->roleService->deleteIdIn($listIdRole);

        // Assert
        self::assertEquals(20, Role::count());
    }

    public function testDeleteByIdSuccess()
    {
        // Arrange
        $this->seed(RoleSeeder::class);
        $role_id = 1;

        // Action
        $this->roleService->deleteById($role_id);

        // Assert
        self::assertEquals(24, Role::count());
    }

    public function testDeleteByIdFail()
    {
        // Arrange
        $this->seed(RoleSeeder::class);
        $role_id = 1;
        $this->roleService->deleteById($role_id);

        // Action
        $this->roleService->deleteById($role_id);

        // Assert
        self::assertEquals(24, Role::count());
    }

    public function testUpdateRole()
    {
        $this->seed(RoleSeeder::class);

        $id = 1;
        $data = [
            'name' => 'RO_SUPAMIN',
            'description' => 'Role superadmin'
        ];

        $update_role = $this->roleService->updateRole($id, $data);

        self::assertNotNull($update_role);
    }


    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from roles');

        $this->roleService = $this->app->make(RoleService::class);
    }
}

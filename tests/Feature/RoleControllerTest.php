<?php

namespace Tests\Feature;

use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Services\RoleService;
use App\Models\Role;

class RoleControllerTest extends TestCase
{

    public function testPageableRoles()
    {
        // Arrange
        $this->seed(RoleSeeder::class);

        // Action
        // Assert
        $this->post('/roles/search', [
            'search' => [],
            'sort' => 'id',
            'order' => 'desc',
            'limit' => 10,
            'offset' => 1
        ])
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['status', 'message', 'data', 'total', 'errors'])
                ->where('status', 'success')
                ->where('total', 25)
                ->count('data', 10)
            );
    }

    public function testGetRole()
    {
        $this->seed(RoleSeeder::class);

        $id = $this->roleService->selectIdByName('RO_USER');

        $this->get("/roles/$id")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['name' => 'RO_USER']);

    }

    public function testCreateRoleSuccess()
    {
        // Arrange
        // Action
        // Assert
        $this->post('/roles', [
            'name' => 'RO_TESTING',
            'description' => 'This role is just for testing'
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(fn (AssertableJson $json) => $json->where('errors', null)
                ->where('status', 'success')
                ->etc()
            );
    }

    public function testCreateRoleSuccessLowerCase()
    {
        // Arrange
        // Action
        // Assert
        $this->post('/roles', [
            'name' => 'ro_testing',
            'description' => 'This role is just for testing'
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(fn (AssertableJson $json) => $json->where('errors', null)
                ->where('status', 'success')
                ->etc()
            );
    }

    public function testCreateRoleFailed()
    {
        // Arrange
        $this->testCreateRoleSuccess();

        // Action
        // Assert
        $this->post('/roles', [
            'name' => 'RO_TESTING',
            'description' => 'This role is just for testing'
        ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(fn (AssertableJson $json) => $json->where('status', 'fail')
                ->whereNot('errors', null)
                ->etc()
            );
    }

    public function testDeleteBatchRoleSuccess()
    {
        // Arrange
        $this->seed(RoleSeeder::class);

        // Action
        // Assert
        $this->post('/roles/destroy', [
            'data' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->where('status', 'success')
                ->where('message', 'The record has been successfully deleted.')
                ->where('errors', null)
                ->etc()
            );

    }

    public function testDeleteBatchRoleFail()
    {
        // Arrange
        $this->seed(RoleSeeder::class);
        $this->post('/roles/destroy', [
            'data' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        ]);

        // Action
        // Assert
        $this->post('/roles/destroy', [
            'data' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(fn (AssertableJson $json) => $json->where('status', 'fail')
                ->where('message', 'error')
                ->whereNot('errors', null)
                ->etc()
            );
    }

    public function testDeleteRoleSuccess()
    {
        // Arrange
        $this->seed(RoleSeeder::class);

        // Action
        // Assert
        $this->delete('/roles/destroy/1')
            ->assertStatus(Response::HTTP_OK);

        self::assertCount(24, Role::all());
    }

    public function testDeleteRoleFail()
    {
        // Arrange
        $this->seed(RoleSeeder::class);
        $this->delete('/roles/destroy/1');

        // Action
        // Assert
        $this->delete('/roles/destroy/1')
            ->assertStatus(Response::HTTP_NOT_FOUND);

        self::assertCount(24, Role::all());
    }

    public function testUpdateRoleSuccess()
    {
        $this->seed(RoleSeeder::class);

        $id = 1;
        $data = [
            'name' => 'RO_SUPAMIN',
            'description' => 'Role superadmin'
        ];

        $this->put("/roles/$id", $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('errors', null)
                ->where('message', 'The record has been successfully updated.')
                ->has('data', fn (AssertableJson $json) => $json->missingAll([
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'created_by',
                    'updated_by',
                    'deleted_by'
                ])
                ->etc())
                ->etc());
    }


    private RoleService $roleService;

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from roles');

        $this->roleService = $this->app->make(RoleService::class);
    }

}

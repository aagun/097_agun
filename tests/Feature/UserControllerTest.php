<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Database\Seeders\RoleSeeder;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User;
use Illuminate\Support\Str;
use Database\Seeders\UserSeeder;

class UserControllerTest extends TestCase
{

    private RoleService $roleService;

    public function testCreateUser()
    {
        $this->seed([RoleSeeder::class]);

        $this->post('/users', [
            'full_name' => 'Agun Fahmi Nurhakiki',
            'nickname' => 'Agun',
            'phone_number' => '+621234567890',
            'gender' => 'l',
            'birth_date' => '1997-09-25',
            'address' => 'Indramayu',
            'email' => 'agun@duck.com',
            'password' => 'Agun!234'
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['status', 'message', 'data', 'errors']));

        $role_id = $this->roleService->selectIdByName('RO_NAME');
        $this->assertDatabaseHas(
            'users',
            [
                'email' => 'agun@duck.com',
                'role_id' => $role_id
            ]
        );
    }

    public function testSearchUser()
    {
        $this->seed([RoleSeeder::class, UserSeeder::class]);
        $this->post('/users/search', [
            'search' => [],
            'sort' => 'id',
            'order' => 'desc',
            'limit' => 10,
            'offset' => 1
        ])->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateUserSuccess()
    {
        $this->seed(RoleSeeder::class);

        $this->post('/users', [
            'full_name' => 'Agun Fahmi Nurhakiki',
            'nickname' => 'Agun',
            'phone_number' => '+621234567890',
            'gender' => 'l',
            'birth_date' => '1997-09-25',
            'address' => 'Indramayu',
            'email' => 'agun@duck.com',
            'password' => 'Agun!234'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'agun@duck.com']);

        $userId = (User::select('id')->where('email', '=', 'agun@duck.com')->first())->id;

        $this->put("/users/$userId", [
            'full_name' => 'Agun Fahmi Nurhakiki',
            'nickname' => 'Agun Ganti Nickname',
            'phone_number' => '+621234567890',
            'gender' => 'l',
            'birth_date' => '1997-09-25',
            'address' => 'Indramayu',
            'email' => 'agun@duck.com',
            'password' => 'Agun!234'
        ])->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', ['nickname' => 'Agun Ganti Nickname']);
    }

    public function testUpdateUserNotExist()
    {
        $this->seed(RoleSeeder::class);

        $userId = Str::uuid();

        $this->put("/users/$userId", [
            'full_name' => 'Agun Fahmi Nurhakiki',
            'nickname' => 'Agun Ganti Nickname',
            'phone_number' => '+621234567890',
            'gender' => 'l',
            'birth_date' => '1997-09-25',
            'address' => 'Indramayu',
            'email' => 'agun@duck.com',
            'password' => 'Agun!234'
        ])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateUserFailed()
    {
        $this->seed(RoleSeeder::class);

        $this->post('/users', [
            'full_name' => 'Agun Fahmi Nurhakiki',
            'nickname' => 'Agun',
            'phone_number' => '+621234567890',
            'gender' => 'l',
            'birth_date' => '1997-09-25',
            'address' => 'Indramayu',
            'email' => 'agun@duck.com',
            'password' => 'Agun!234'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'agun@duck.com']);

        $userId = (User::select('id')->where('email', '=', 'agun@duck.com')->first())->id;

        $this->put("/users/$userId", [
            'full_name' => 'Agun Fahmi Nurhakiki',
            'nickname' => 'Agun Ganti Nickname',
            'phone_number' => '+621234567890',
            'gender' => 'laki-laki',
            'birth_date' => '1997-09-25',
            'address' => 'Indramayu',
            'email' => 'agun@duck.com',
            'password' => 'Agun!234'
        ])->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testDeleteUserExist()
    {
        $this->testCreateUser();
        $userId = (User::select('id')->where('email', '=', 'agun@duck.com')->first())->id;

        $this->delete("/users/$userId")
            ->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteUserNotExist()
    {
        $this->seed(RoleSeeder::class);

        $userId = Str::uuid();

        $this->delete("/users/$userId")
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetUser()
    {
        $this->testCreateUser();

        $user = User::query()->first();

        $this->get("/users/$user->id")
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['name' => 'RO_USER']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from users');
        DB::delete('delete from roles');

        $this->roleService = $this->app->make(RoleService::class);
        $this->userService = $this->app->make(UserService::class);
    }
}

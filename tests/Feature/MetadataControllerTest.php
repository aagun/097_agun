<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\UserService;
use App\Services\DebtService;
use App\Services\TransactionService;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;

class MetadataControllerTest extends TestCase
{
    private UserService $userService;

    private DebtService $debtService;

    private TransactionService $transactionService;

    public function testGetMetadata()
    {
        $this->get('/metadata')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', fn (AssertableJson $json) => $json->hasAll(['total_users', 'total_debt', 'last_income']))
                ->etc()
            );

    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->app->make(UserService::class);
        $this->debtService = $this->app->make(DebtService::class);
        $this->transactionService = $this->app->make(TransactionService::class);
    }


}

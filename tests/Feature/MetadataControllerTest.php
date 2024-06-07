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

    public function testDailyIncome()
    {
        $request = [
            'search' => [],
            'sort' => 'id',
            'order' => 'desc',
            'limit' => 10,
            'offset' => 1
        ];

        $response = $this->post('/metadata/daily', $request);
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                return $json->hasAll(['status', 'message', 'data', 'total', 'errors'])
                    ->whereNot('data', null);
            });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->app->make(UserService::class);
        $this->debtService = $this->app->make(DebtService::class);
        $this->transactionService = $this->app->make(TransactionService::class);
    }


}

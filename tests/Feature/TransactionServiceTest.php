<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\TransactionService;
use App\Enums\TransactionType;
use App\Http\Requests\PageableRequest;

class TransactionServiceTest extends TestCase
{
    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = $this->app->make(TransactionService::class);
    }

    public function testSumTransactionAmountByTransactionType()
    {
        $total_transaction_amount = $this->transactionService
            ->sumTransactionAmountByTransactionType(TransactionType::PAY_DEBT);
        self::assertNotNull($total_transaction_amount);
    }

    public function testDailyIncome()
    {
        $request = new PageableRequest([
            'search' => [],
            'sort' => 'id',
            'order' => 'desc',
            'limit' => 10,
            'offset' => 1
        ]);
        $result = $this->transactionService->dailyIncome($request);
        self::assertNotNull($result);
    }

    public function testMonthlyIncome()
    {
        $monthlyIncome = $this->transactionService->monthlyIncome();
        self::assertNotNull($monthlyIncome);
    }


}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\TransactionService;
use App\Enums\TransactionType;

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

}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\DebtService;

class DebtTest extends TestCase
{
    private DebtService $debtService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->debtService = $this->app->make(DebtService::class);
    }


    public function testSumRemainingDebt()
    {
        $total_remaining_debt = $this->debtService->sumRemainingDebt();
        self::assertNotNull($total_remaining_debt);
    }

}

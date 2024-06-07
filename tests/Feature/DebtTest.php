<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\DebtService;
use App\Models\Debt;
use Illuminate\Support\Facades\DB;

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

    public function testCountDistinct()
    {
        $count = Debt::query()
            ->select(DB::raw('COUNT(DISTINCT user_id) AS total, user_id'))
            ->groupBy('user_id')
        ->get();
        self::assertNotNull($count);
    }


}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Enums\DebtType;
use App\Enums\TransactionType;
use Illuminate\Http\Response;
use App\Services\UserService;
use App\Services\DebtService;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Debt;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;

class TransactionControllerTest extends TestCase
{
    private UserService $userService;

    private DebtService $debtService;

    public function testCreateBatchNewDebtTransactionSuccess()
    {
        $this->seed([
            RoleSeeder::class,
            UserSeeder::class
        ]);

        $user = $this->userService->findOneRandom();
        $user_id = $user->id;
        $this->post('/transactions', [
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::FLEXIBLE,
                'transaction_amount' => 1_200_000,
                'description' => 'Beli TV',
                'installment_number' => null,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 2_400_000,
                'description' => 'Beli Kulkas',
                'installment_number' => null,
                'total_installments' => 10
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::SEASON,
                'transaction_amount' => 22_200_000,
                'description' => 'Beli Motor',
                'installment_number' => null,
                'total_installments' => null
            ]
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['status', 'message', 'data', 'errors']));

        self::assertEquals(
            3,
            Debt::where("user_id", $user_id)->count()
        );

        $monthly_debt = Debt::where("user_id", $user_id)->where("debt_type", DebtType::MONTHLY)->first();
        self::assertEquals(10, $monthly_debt->detailInstallments()->count());

        $flexible_debt = Debt::where("user_id", $user_id)->where("debt_type", DebtType::FLEXIBLE)->first();
        self::assertEquals(1, $flexible_debt->detailInstallments()->count());

        $season_debt = Debt::where("user_id", $user_id)->where("debt_type", DebtType::SEASON)->first();
        self::assertEquals(1, $season_debt->detailInstallments()->count());

        $this->assertDatabaseHas('transactions', [
            ['transaction_type', 'new_debt'],
            ['user_id', $user_id],
            ['description', 'Beli Kulkas'],
            ['transaction_amount', 2_400_000]
        ]);

        $this->assertDatabaseHas('transactions', [
            ['transaction_type', 'new_debt'],
            ['user_id', $user_id],
            ['description', 'Beli Motor'],
            ['transaction_amount', 22_200_000]
        ]);

        $this->assertDatabaseHas('transactions', [
            ['transaction_type', 'new_debt'],
            ['user_id', $user_id],
            ['description', 'Beli TV'],
            ['transaction_amount', 1_200_000]
        ]);

    }

    public function testCreateBatchNewDebtTransactionFailed()
    {

        $user_id = "9c31dbac-3483-42c0-85c7-c15ee9912654";
        $this->post('/transactions', [
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 1_200_000,
                'description' => 'Beli TV',
                'installment_number' => null,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 2_400_000,
                'description' => 'Beli Kulkas',
                'installment_number' => null,
                'total_installments' => null
            ]
        ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(fn (AssertableJson $json) => $json
                ->whereNot('errors', null)
                ->etc()
            );
    }

    public function testCreateBatchPayDebtTransactionSuccess()
    {
        $this->testCreateBatchNewDebtTransactionSuccess();

        $debt = $this->debtService->findOneByDebtType(DebtType::MONTHLY);
        $user_id = $debt->user_id;

        $flexible_debt = $this->debtService->findByUserIdAndDebtType($user_id, DebtType::FLEXIBLE)->first();
        $season_debt = $this->debtService->findByUserIdAndDebtType($user_id, DebtType::SEASON)->first();
        $monthly_debt = $this->debtService->findByUserIdAndDebtType($user_id, DebtType::MONTHLY)->first();

        $this->post('/transactions', [
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::PAY_DEBT,
                'debt_id' => $flexible_debt->id,
                'debt_type' => DebtType::FLEXIBLE,
                'transaction_amount' => ($flexible_debt->detailInstallments()->first())->amount,
                'description' => "Bayar hutang",
                'installment_number' => null,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::PAY_DEBT,
                'debt_id' => $monthly_debt->id,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 240_000,
                'description' => 'Bayar angsuran Kulkas ke-1',
                'installment_number' => 1,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::PAY_DEBT,
                'debt_id' => $monthly_debt->id,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 240_000,
                'description' => 'Bayar angsuran Kulkas ke-2',
                'installment_number' => 2,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::PAY_DEBT,
                'debt_id' => $season_debt->id,
                'debt_type' => DebtType::SEASON,
                'transaction_amount' => 22_200_000,
                'description' => 'Bayar Beli Motor',
                'installment_number' => null,
                'total_installments' => null
            ]
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['status', 'message', 'data', 'errors']));

        // Check user debt
        $user_debt = Debt::where("user_id", $user_id)->get();
        self::assertEquals(
            1,
            $user_debt->count()
        );

        // Check user paid installments for monthly debt
        $monthly_debt = $user_debt->first()->detailInstallments();
        self::assertEquals(2, $monthly_debt->onlyTrashed()->count());
    }

    public function testCreateBatchTransactionSuccess()
    {
        $this->testCreateBatchPayDebtTransactionSuccess();

        $debt = $this->debtService->findOneByDebtType(DebtType::MONTHLY);
        $user_id = $debt->user_id;

        $flexible_debt = $this->debtService->findByUserIdAndDebtType($user_id, DebtType::FLEXIBLE)->first();
        $season_debt = $this->debtService->findByUserIdAndDebtType($user_id, DebtType::SEASON)->first();
        $monthly_debt = $this->debtService->findByUserIdAndDebtType($user_id, DebtType::MONTHLY)->first();

        $this->post('/transactions', [
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::FLEXIBLE,
                'transaction_amount' => 5_000_000,
                'description' => 'Beli Mesin Cuci',
                'installment_number' => null,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::PAY_DEBT,
                'debt_id' => $monthly_debt->id,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 240_000,
                'description' => 'Bayar angsuran Kulkas ke-3',
                'installment_number' => 3,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::PAY_DEBT,
                'debt_id' => $monthly_debt->id,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 240_000,
                'description' => 'Bayar angsuran Kulkas ke-4',
                'installment_number' => 4,
                'total_installments' => null
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::MONTHLY,
                'transaction_amount' => 240_000_000,
                'description' => 'Beli Mobil',
                'installment_number' => null,
                'total_installments' => 12
            ],
            [
                'user_id' => $user_id,
                'transaction_type' => TransactionType::NEW_DEBT,
                'debt_id' => null,
                'debt_type' => DebtType::SEASON,
                'transaction_amount' => 45_000_000,
                'description' => 'Beli Motor Yamaha NMax',
                'installment_number' => null,
                'total_installments' => null
            ]
        ])
            ->assertStatus(Response::HTTP_CREATED);

        // Check user debt
        $user_debt = Debt::where("user_id", $user_id)->get();
        self::assertEquals(
            4,
            $user_debt->count()
        );

        // Check user paid installments for monthly debt
        $monthly_debt = $user_debt->first()->detailInstallments();
        self::assertEquals(4, $monthly_debt->onlyTrashed()->count());
    }

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from detail_transactions');
        DB::delete('delete from installments');
        DB::delete('delete from debts');
        DB::delete('delete from transactions');
        DB::delete('delete from users');
        DB::delete('delete from roles');

        $this->userService = $this->app->make(UserService::class);
        $this->debtService = $this->app->make(DebtService::class);
    }

}

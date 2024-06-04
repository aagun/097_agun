<?php

namespace Tests\Feature;

use App\Enums\DebtType;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Models\Debt;
use App\Models\Installment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from detail_transactions');
        DB::delete('delete from installments');
        DB::delete('delete from debts');
        DB::delete('delete from transactions');
        DB::delete('delete from users');
        DB::delete('delete from roles');
    }

    public function testInsert()
    {
        // Arrange
        $this->seed([RoleSeeder::class, UserSeeder::class]);

        $employee = User::first();
        $user = User::whereNot('id', $employee->id)->get()->random();

        $transaction = new Transaction();
        $transaction->transaction_type = TransactionType::NEW_DEBT;
        $transaction->transaction_date = now();
        $transaction->transaction_amount = 1_200_000;
        $transaction->installment_number = null;
        $transaction->description = 'Beli TV';
        $transaction->user_id = $user->id;
        $transaction->employee_id = $employee->id;

        // Action
        $is_success = $transaction->save();

        // Assert
        self::assertTrue($is_success);
    }

    public function testDelete()
    {
        // Arrange
        $this->seed([RoleSeeder::class, UserSeeder::class, TransactionSeeder::class]);

        $transaction = Transaction::all()->random();

        // Action
        $is_success = $transaction->delete();

        // Assert
        self::assertTrue($is_success);
        self::assertNull(Transaction::find($transaction->id));
    }

    public function testManyToManyNew()
    {
        // Arrange
        $this->seed([RoleSeeder::class, UserSeeder::class]);

        $employee = User::first();
        $user = User::whereNot('id', $employee->id)->get()->random();

        $transaction = new Transaction([
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'transaction_date' => now(),
            'transaction_type' => TransactionType::NEW_DEBT,
            'description' => 'Beli TV',
            'installment_number'=> null,
            'total_installments' => 6,
            'transaction_amount' => 1_000_000,
        ]);

        $transaction->save();

        $debt = new Debt([
            'user_id' => $user->id,
            'debt_type' => DebtType::MONTHLY,
            'total_debt' => $transaction->transaction_amount,
            'remaining_debt' => $transaction->transaction_amount
        ]);
        $debt->save();

        $installment_amount = $transaction->transaction_amount / $transaction->total_installments;
        for ($i = 1; $i <= $transaction->total_installments; $i++) {
            $installment = new Installment([
                'installment_number' => $i,
                'amount' => $installment_amount,
                'due_date' => (Carbon::now())->addMonth($i),
                'status' => PaymentStatus::UNPAID,
                'debt_id' => $debt->id
            ]);
            $installment->save();
        }

        $transaction->detailDebts()->attach($debt->id);

        // Action
        // Assert
        self::assertTrue(true);
    }


}

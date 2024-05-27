<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
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
        $transaction->transaction_type = 'NEW_DEBT';
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



}

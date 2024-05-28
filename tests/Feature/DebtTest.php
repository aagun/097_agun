<?php

namespace Tests\Feature;

use App\Enums\DebtType;
use App\Models\Debt;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DebtTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from detail_transactions');
        DB::delete('delete from debts');
        DB::delete('delete from users');
        DB::delete('delete from roles');
    }

    public function testInsert()
    {
        // Arrange
        $this->seed([RoleSeeder::class, UserSeeder::class]);

        $user = DB::table('users')
            ->limit(25)
            ->offset(1)
            ->get()
            ->random();

        $newDebt = new Debt([
            'debt_type' => DebtType::FLEXIBLE,
            'user_id' => $user->id
        ]);

        // Action
        $is_succes = $newDebt->save();

        // Assert
        self::assertTrue($is_succes);

        $debt = Debt::find($newDebt->id);

        Log::info('Debt: ' . $debt->toJson(JSON_PRETTY_PRINT));

        self::assertNotNull($debt);
        self::assertEquals(DebtType::FLEXIBLE, $debt->debt_type);
        self::assertNotNull($debt->user);

    }

}

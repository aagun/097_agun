<?php

namespace App\Services\Impl;

use App\Services\DebtService;
use App\Models\Debt;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\DebtType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DebtServiceImpl implements DebtService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findById(string $id): Debt
    {
        return Debt::where('id', $id)->first();
    }

    public function findOneByDebtType(DebtType $debt_type): Builder | Model
    {
        return Debt::query()->where('debt_type', $debt_type)->first();
    }

    public function findByUserIdAndDebtType(string $user_id, DebtType $debt_id): array | Collection
    {
        return Debt::query()
            ->where('user_id', $user_id)
            ->where('debt_type', $debt_id)
            ->where('deleted_at', null)
            ->where('deleted_by', null)
            ->get();
    }

    public function existByUserIdAndDebtType(string $user_id, DebtType $debt_id): bool
    {
        return Debt::query()
            ->where('user_id', $user_id)
            ->where('debt_type', $debt_id)
            ->where('deleted_at', null)
            ->where('deleted_by', null)
            ->exists();
    }


    public function existByIdAndDebtType(string $id, DebtType $debt_type): bool
    {
        return Debt::where('id', $id)
            ->where('debt_type', $debt_type)
            ->exists();
    }

    public function exists(string $id): bool
    {
        return Debt::where('id', $id)->exists();
    }

    public function updateDebt()
    {

    }
}

<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Enums\DebtType;
use App\Models\Debt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface DebtService
{
    public function findById(string $id): Debt;

    public function findOneByDebtType(DebtType $debt_type): Builder | Model;

    public function findByUserIdAndDebtType(string $user_id, DebtType $debt_id): array | Collection;

    public function existByUserIdAndDebtType(string $user_id, DebtType $debt_id): bool;

    public function exists(string $id): bool;

    public function existByIdAndDebtType(string $id, DebtType $debt_type): bool;

    public function sumRemainingDebt(): int;
}

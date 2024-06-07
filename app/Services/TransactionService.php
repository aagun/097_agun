<?php

namespace App\Services;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\PageableRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransactionService
{
    public function sumTransactionAmountByTransactionType(TransactionType $transaction_type): int;

    public function dailyIncome(PageableRequest $request): LengthAwarePaginator;

    public function monthlyIncome(): array | Collection;
}

<?php

namespace App\Services\Impl;

use App\Services\TransactionService;
use App\Models\Transaction;
use App\Enums\TransactionType;

class TransactionServiceImpl implements TransactionService
{
    public function sumTransactionAmountByTransactionType(TransactionType $transcation_type): int
    {
        return Transaction::query()
            ->whereRaw('DATE(transaction_date) = CURRENT_DATE')
            ->where('transaction_type', $transcation_type)
            ->sum('transaction_amount');
    }

}

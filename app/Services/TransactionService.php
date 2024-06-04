<?php

namespace App\Services;

use App\Enums\TransactionType;

interface TransactionService
{
    public function sumTransactionAmountByTransactionType(TransactionType $transaction_type): int;
}

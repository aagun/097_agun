<?php

namespace App\Enums;

enum TransactionType: string
{
    case NEW_DEBT = 'new_debt';
    case PAY_DEBT = 'pay_debt';
}

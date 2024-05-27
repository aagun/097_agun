<?php

namespace App\Enums;

enum DebtType: string
{
    case FLEXIBLE = 'flexible';
    case MONTHLY = 'monthly';
    case SEASON = 'season';

}

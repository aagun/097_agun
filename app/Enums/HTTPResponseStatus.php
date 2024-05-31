<?php

namespace App\Enums;

enum HTTPResponseStatus: string
{
    case SUCCESS = 'success';
    case FAIL = 'fail';
}

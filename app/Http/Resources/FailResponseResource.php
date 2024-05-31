<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\HTTPResponseStatus;

class FailResponseResource extends BaseResponseResource
{
    public function __construct(string $message, mixed $errors = null, $resource = null)
    {
        parent::__construct(HTTPResponseStatus::FAIL->value, $message, $errors, $resource);
    }

}

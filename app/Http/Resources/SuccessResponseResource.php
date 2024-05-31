<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\HTTPResponseStatus;

class SuccessResponseResource extends BaseResponseResource
{
    public function __construct(string $message, $resource = null)
    {
        parent::__construct(HTTPResponseStatus::SUCCESS->value, $message, null, $resource);
    }

}

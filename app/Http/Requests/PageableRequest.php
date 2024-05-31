<?php

namespace App\Http\Requests;

use App\Rules\CaseInsensitiveOrder;

class PageableRequest extends BaseExceptionRequest
{
    public function rules(): array
    {
        return [
            'offset' => ['sometimes', 'numeric'],
            'limit' => ['sometimes', 'numeric'],
            'order' => ['sometimes', 'string', new CaseInsensitiveOrder()],
            'sort' => ['sometimes', 'string'],
            'search' => ['sometimes', 'array']
        ];
    }
}

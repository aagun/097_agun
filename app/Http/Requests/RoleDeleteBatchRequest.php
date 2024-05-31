<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RoleDeleteBatchRequest extends BaseExceptionRequest
{
    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'data.*.id' => ['required', 'numeric', Rule::exists('roles', 'id')->whereNull('deleted_at')],
        ];
    }
}

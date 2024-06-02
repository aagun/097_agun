<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Rules\ArrayHasAtLeastOneElement;

class RoleCreateBatchRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'data' => ['array', new ArrayHasAtLeastOneElement()],
            'data.*.name' => [
                'required',
                'string',
                'min:4',
                'max:15',
                'starts_with:RO_,ro_,rO_,Ro_',
                Rule::unique('roles', 'name')
            ],
            'data.*.description' => [
                'required',
                'string'
            ]
        ];
    }
}

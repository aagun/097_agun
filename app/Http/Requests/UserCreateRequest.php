<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Enums\Gender;
use Illuminate\Validation\Rules\Password;

class UserCreateRequest extends BaseRequest
{
    public function except($keys)
    {

    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'min:1', 'max:100', 'string'],
            'nickname' => ['required', 'string', 'min:1', 'max:100'],
            'phone_number' => ['required', 'string', 'min:10', 'max:20'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'birth_date' => ['sometimes', 'date_format:Y-m-d', 'before_or_equal:today'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email', 'string'],
            'password' => [
                'required',
                'string',
                Password::min(6)
                    ->max(32)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
            ]
        ];
    }
}

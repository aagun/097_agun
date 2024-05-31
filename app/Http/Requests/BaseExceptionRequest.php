<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\BaseResponseResource;
use Illuminate\Http\Response;

class BaseExceptionRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response(
            new BaseResponseResource(
                'fail',
                'error',
                $validator->getMessageBag()
            ), Response::HTTP_BAD_REQUEST));
    }
}

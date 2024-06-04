<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

if (!function_exists('show_route')) {

    function generatePhoneNumber(): string
    {
        $phone_number = fake()->phoneNumber();
        $phone_number = preg_replace("/[+.()]/", "", $phone_number);
        return substr($phone_number, 0, 13);
    }

    function convertString($input) {
        $input = strtoupper($input);
        $input = preg_replace('/\./', '', $input);
        $input = preg_replace('/\s+/', '_', $input);
        return $input;
    }

    function validateExistenceDataById(mixed $id, mixed $serviceClass): void
    {
        if (!$serviceClass->exists($id)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "The id [$id] is not found"
                    ]
                ]
            ], Response::HTTP_NOT_FOUND));
        }
    }

    function toEnum($value, $enumClass)
    {
        return $value instanceof $enumClass ? $value : $enumClass::from($value);
    }
}

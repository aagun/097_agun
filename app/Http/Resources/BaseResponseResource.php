<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResponseResource extends JsonResource
{
    public string $status;
    public string $message;
    public mixed $errors;

    public function __construct(string $status, string $message, mixed $errors = null, $resource = null)
    {
        parent::__construct($resource);

        $this->status = $status;
        $this->message = $message;
        $this->errors = $errors;
    }

    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->resource,
            'errors' => $this->errors
        ];
    }
}

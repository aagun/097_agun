<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BasePageableResponseCollection extends ResourceCollection
{
    private string $status;
    private string $message;
    private mixed $errors;
    private mixed $data;

    public function __construct(string $status, string $message, $data, $resource, $errors)
    {
        parent::__construct($resource);

        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
    }

    public function paginationInformation($request, $paginated, $default)
    {
        $default['total'] = $default['meta']['total'];

        unset($default['links']);
        unset($default['meta']);

        return $default;
    }

    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }
}

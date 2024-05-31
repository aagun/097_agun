<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use App\Enums\HTTPResponseStatus;

class SuccessPageableResponseCollection extends BasePageableResponseCollection
{
    public function __construct(string $message, $resource, $collectionClass, $errors = null)
    {
        parent::__construct(
            HTTPResponseStatus::SUCCESS->value,
            $message,
            $collectionClass::collection($resource),
            $resource,
            $errors
        );
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return [
    //         'data' => RoleResource::collection($this->collection),
    //     ];
    // }
}

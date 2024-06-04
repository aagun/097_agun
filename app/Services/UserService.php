<?php

namespace App\Services;

use App\Models\User;
use App\Http\Requests\PageableRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserService
{
    public function saveUser(array $user): void;

    public function deleteById(string $id): void;

    public function update(string $id, array $user): User;

    public function exists(string $id): bool;

    public function findUserPageable(PageableRequest $request): LengthAwarePaginator;

    public function findUserById(string $id): User;

    public function findOneRandom(): User;
}

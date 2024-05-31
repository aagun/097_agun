<?php

namespace App\Services;

use App\Http\Requests\PageableRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Role;
use App\Http\Requests\RoleCreateRequest;

interface RoleService
{
    public function findRoleById(int $id): Role;

    public function findAllRolePageable(PageableRequest $request): LengthAwarePaginator;

    public function saveRole(array $data): void;

    public function saveAll(array $data): void;

    public function deleteIdIn(array $listId): void;

    public function deleteById(int $id): void;

    public function existNotTrashedById(int $id): bool;

    public function updateRole(int $id, array $data): Role;
}

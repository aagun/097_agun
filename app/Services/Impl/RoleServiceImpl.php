<?php

namespace App\Services\Impl;

use App\Services\RoleService;
use App\Models\Role;
use App\Http\Requests\PageableRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleServiceImpl implements RoleService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    public function findRoleById(int $id): Role
    {
        return Role::find($id);
    }

    public function findRoleByName(string $name): Role
    {
        return Role::query()
            ->where('name', $name)
            ->get()
            ->first();
    }

    public function findAllRolePageable(PageableRequest $request): LengthAwarePaginator
    {
        $limit = isset($request->limit) ? $request->limit : 10;
        $offset = isset($request->offset) ? $request->offset : 1;
        $order = isset($request->order) ? $request->order : 'ASC';
        $sort = isset($request->sort) ? $request->sort : 'name';
        $search = $request->search;

        $query = Role::query()
            ->when(isset($search), function (Builder $query) use ($search) {
                $name = $search[ 'name' ] ?? null;
                $filterName = $search[ 'filter_name' ] ?? null;

                $query->where(function ($query) use ($name, $filterName) {
                    if ($name) {
                        $query->whereRaw("(name LIKE CONCAT('%', ?, '%'))", [$name]);
                    }

                    if ($filterName) {
                        $query->WhereRaw("(name LIKE CONCAT('%', ?, '%'))", [$filterName]);
                    }
                });
            })
            ->orderBy($sort, $order);

        return $query->paginate(perPage: $limit, page: $offset);
    }

    public function saveRole(array $data): void
    {
        Role::create($data);
    }

    public function saveAll(array $data): void
    {
        foreach ($data as $item) {
            self::saveRole($item);
        }
    }

    public function deleteIdIn(array $listId): void
    {
        Role::query()->whereIn('id', $listId)->delete();
    }

    public function deleteById(int $id): void
    {
        Role::query()
            ->where([
                ['id', '=', $id],
                ['deleted_at', '=', null]
            ])
            ->delete();
    }

    public function existNotTrashedById(int $id): bool
    {
        return Role::query()
            ->where([
                ['id', '=', $id],
                ['deleted_at', '=', null]
            ])
            ->exists();
    }

    public function exists(int $id): bool
    {
        return Role::query()
            ->where('id', '=', $id)
            ->exists();
    }

    public function updateRole(int $id, array $data): Role
    {
        Role::query()->where('id', $id)->update($data);
        return Role::find($id);
    }

    public function selectIdByName(string $name): int
    {
        $role = Role::query()
            ->select(['id'])
            ->where('name', '=', 'RO_USER')
            ->first();
        return $role->id;
    }

}

<?php

namespace App\Services\Impl;

use App\Services\UserService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Requests\PageableRequest;
use Illuminate\Database\Eloquent\Builder;

class UserServiceImpl implements UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function saveUser(array $user): void
    {
        User::query()->create($user);
    }

    public function deleteById(string $id): void
    {
        User::query()
            ->where('id', '=', $id)
            ->where('deleted_at', '=', null)
            ->delete();
    }

    public function update(string $id, array $user): User
    {
        User::query()->where('id', $id)->update($user);
        return User::find($id);
    }

    public function exists(string $id): bool
    {
        return User::query()->where('id', $id)->exists();
    }

    public function findUserPageable(PageableRequest $request): LengthAwarePaginator
    {
        $limit = isset($request->limit) ? $request->limit : 10;
        $offset = isset($request->offset) ? $request->offset : 1;
        $order = isset($request->order) ? $request->order : 'ASC';
        $sort = isset($request->sort) ? $request->sort : 'full_name';
        $search = $request->search;

        $query = User::query()
            ->when(isset($search), function (Builder $query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $this->findUserPageableFilter($query, $search);
                });
            })
            ->orderBy($sort, $order);

        return $query->paginate(perPage: $limit, page: $offset);
    }

    private function findUserPageableFilter(Builder $query, array $search): void
    {
        $full_name = $search[ 'name' ] ?? null;
        $nickname = $search[ 'nickname' ] ?? null;
        $phone_number = $search[ 'phone_number' ] ?? null;
        $address = $search[ 'address' ] ?? null;

        if ($full_name) {
            $query->whereRaw("(full_name LIKE CONCAT('%', ?, '%'))", [$full_name]);
        }

        if ($nickname) {
            $query->WhereRaw("(name LIKE CONCAT('%', ?, '%'))", [$nickname]);
        }

        if ($phone_number) {
            $query->WhereRaw("(phone_number LIKE CONCAT('%', ?, '%'))", [$phone_number]);
        }

        if ($address) {
            $query->WhereRaw("(phone_number LIKE CONCAT('%', ?, '%'))", [$address]);
        }
    }

    public function findUserById(string $id): User
    {
        return User::find($id);
    }

    public function findOneRandom(): User
    {
        return User::all()
            ->random();
    }

    public function countByRoleName(string $role_name): int
    {
        return User::query()
            ->join('roles', 'users.role_id', 'roles.id')
            ->where('roles.name', $role_name)
            ->count();
    }

}

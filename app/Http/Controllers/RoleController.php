<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RoleCreateRequest;
use Illuminate\Http\Response;
use App\Http\Resources\BaseResponseResource;
use App\Http\Requests\RoleCreateBatchRequest;
use App\Services\RoleService;
use App\Http\Requests\PageableRequest;
use App\Http\Requests\RoleDeleteBatchRequest;
use App\Http\Resources\SuccessResponseResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\SuccessPageableResponseCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleController extends Controller
{
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function searchRoles(PageableRequest $request): ResourceCollection
    {
        $resource = $this->roleService->findAllRolePageable($request);
        return new SuccessPageableResponseCollection(
            'List role',
            $resource,
            RoleResource::class
        );
    }

    public function createRole(RoleCreateRequest $request): Response
    {
        $data = $request->validated();

        $this->roleService->saveRole($data);

        return response(
            new BaseResponseResource(
                'success',
                'The record has been successfully created.'),
            Response::HTTP_CREATED);
    }

    public function createBatchRole(RoleCreateBatchRequest $request): Response
    {
        $data = $request->validated()[ 'data' ];
        $this->roleService->saveAll($data);

        return response(
            new BaseResponseResource(
                'success',
                'The record has been successfully created.'),
            Response::HTTP_CREATED);
    }

    public function deleteBatchRole(RoleDeleteBatchRequest $request): Response
    {
        $payload = $request->validated()[ 'data' ];
        $roleIds = collect($payload)->map(fn (array $item) => $item[ 'id' ])->toArray();

        $this->roleService->deleteIdIn($roleIds);

        return response(
            new BaseResponseResource(
                'success',
                'The record has been successfully deleted.'),
            Response::HTTP_OK);
    }

    public function deleteRole(Request $request, int $roleId): Response
    {
        validateExistenceDataById($roleId, $this->roleService);

        $this->roleService->deleteById($roleId);
        return response(new SuccessResponseResource('The record has been successfully deleted.'));
    }

    public function updateRole(RoleCreateRequest $request, int $id): Response
    {
        validateExistenceDataById($id, $this->roleService);

        $data = $request->validated();
        $role = $this->roleService->updateRole($id, $data);
        return response(new SuccessResponseResource('The record has been successfully updated.', new RoleResource($role)));
    }

    public function getRole(int $id): Response
    {
        $role = $this->roleService->findRoleById($id);
        return response(new SuccessResponseResource(
            'Role found',
            new RoleResource($role)
        ));
    }
}

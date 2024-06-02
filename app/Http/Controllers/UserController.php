<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Services\RoleService;
use App\Services\UserService;
use App\Http\Resources\SuccessResponseResource;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\FailResponseResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\PageableRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\SuccessPageableResponseCollection;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private RoleService $roleService;
    private UserService $userService;

    public function __construct(RoleService $roleService, UserService $userService)
    {
        $this->roleService = $roleService;
        $this->userService = $userService;
    }

    public function createUser(UserCreateRequest $request): Response
    {
        $payload = $request->validated();
        $payload[ 'role_id' ] = $this->roleService->selectIdByName('RO_USER');
        $this->userService->saveUser($payload);

        return response(
            new SuccessResponseResource(
                'The record has been successfully created.'
            ),
            Response::HTTP_CREATED
        );
    }

    public function updateUser(UserCreateRequest $request, string $id): Response
    {
        $payload = $request->validated();

        if (!$this->userService->exists($id)) {
            throw new HttpResponseException(response(new FailResponseResource(
                'error',
                ["message" => ["The id [$id] is not found"]]
            ), Response::HTTP_NOT_FOUND));
        }

        $updatedUser = $this->userService->update($id, $payload);

        return response(new SuccessResponseResource(
            'The record has been successfully updated.',
            new UserResource($updatedUser)
        ));
    }

    public function deleteUser(string $id): void
    {
        validateExistenceDataById($id, $this->userService);

        $this->userService->deleteById($id);
    }

    public function searchUser(PageableRequest $request): ResourceCollection
    {
        $resource = $this->userService->findUserPageable($request);
        return new SuccessPageableResponseCollection(
            'List user',
            $resource,
            UserResource::class
        );
    }

    public function getUser(Request $request, string $id): Response
    {
        validateExistenceDataById($id, $this->userService);
        $user = $this->userService->findUserById($id);
        return response(new SuccessResponseResource(
            'Success retrieve detail user. ',
            new UserResource($user)
        ));
    }
}

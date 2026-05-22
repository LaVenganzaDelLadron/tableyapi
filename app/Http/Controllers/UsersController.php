<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    private const RELATIONS = ['carts', 'orders', 'reviews', 'notifications'];

    public function index(Request $request): JsonResponse
    {
        $users = User::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Users retrieved successfully.', $users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated())->load(self::RELATIONS);

        return $this->success('User created successfully.', $user, 201);
    }

    public function show(User $user): JsonResponse
    {
        return $this->success('User retrieved successfully.', $user->load(self::RELATIONS));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return $this->success('User updated successfully.', $user->load(self::RELATIONS));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->success('User deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['roles', 'customerProfile']);

        if ($request->filled('status'))  $query->byStatus($request->status);
        if ($request->filled('role'))    $query->whereHas('roles', fn($q) => $q->where('slug', $request->role));
        if ($request->filled('search'))  $query->where(fn($q) => $q->where('first_name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%"));

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->get('per_page', 15))->through(fn($u) => new UserResource($u)));
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource($user->load(['roles', 'customerProfile'])));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'phone'      => 'nullable|string|max:20|unique:users',
            'password'   => 'required|string|min:8',
            'status'     => 'sometimes|in:active,inactive,suspended',
            'roles'      => 'sometimes|array',
            'roles.*'    => 'exists:roles,id',
        ]);

        $user = User::create($data);
        if (!empty($data['roles'])) $user->roles()->sync($data['roles']);

        return $this->created(new UserResource($user->load(['roles', 'customerProfile'])));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name'  => 'sometimes|string|max:100',
            'email'      => 'sometimes|email|unique:users,email,' . $user->id,
            'phone'      => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'status'     => 'sometimes|in:active,inactive,suspended,banned',
        ]);

        $user->update($data);
        return $this->success(new UserResource($user->fresh()->load(['roles', 'customerProfile'])));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return $this->noContent('User deactivated.');
    }

    public function updateRoles(Request $request, User $user): JsonResponse
    {
        $data = $request->validate(['roles' => 'required|array', 'roles.*' => 'exists:roles,id']);
        $user->roles()->sync($data['roles']);
        return $this->success(new UserResource($user->fresh()->load('roles')));
    }
}

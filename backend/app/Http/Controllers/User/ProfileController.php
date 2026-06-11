<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['customerProfile', 'roles']);
        return $this->success(new UserResource($user));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {

        $user = $request->user();
        $user->update($request->only(['first_name', 'last_name', 'phone']));

        if ($request->has('gender') || $request->has('birth_date')) {
            $user->customerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['gender', 'birth_date'])
            );
        }

        return $this->success(new UserResource($user->fresh()->load(['customerProfile', 'roles'])));
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate(['avatar' => 'required|image|max:2048']);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return $this->success(['avatar' => Storage::disk('public')->url($path)]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect.', 422);
        }

        $user->update(['password' => $request->password]);
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return $this->success(null, 'Password changed successfully.');
    }
}

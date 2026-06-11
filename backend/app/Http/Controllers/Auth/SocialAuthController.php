<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends ApiController
{
    public function redirect(string $provider): JsonResponse
    {
        $this->validateProvider($provider);

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return $this->success(['redirect_url' => $url]);
    }

    public function callback(string $provider): JsonResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $e) {
            return $this->error('Social authentication failed.', 422);
        }

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            // Check if user exists with same email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar'      => $socialUser->getAvatar(),
                ]);
            } else {
                $nameParts = explode(' ', $socialUser->getName(), 2);

                $user = User::create([
                    'first_name'        => $nameParts[0] ?? '',
                    'last_name'         => $nameParts[1] ?? '',
                    'email'             => $socialUser->getEmail(),
                    'provider'          => $provider,
                    'provider_id'       => $socialUser->getId(),
                    'avatar'            => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);

                $customerRole = Role::where('slug', 'customer')->first();
                if ($customerRole) $user->roles()->attach($customerRole->id);

                CustomerProfile::create([
                    'user_id'       => $user->id,
                    'referral_code' => strtoupper(Str::random(8)),
                ]);
            }
        }

        $user->update(['last_login_at' => now()]);
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user'  => new \App\Http\Resources\UserResource($user->load(['customerProfile', 'roles'])),
            'token' => $token,
        ], 'Social login successful.');
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(422, 'Unsupported provider.');
        }
    }
}

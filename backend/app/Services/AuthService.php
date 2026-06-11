<?php

namespace App\Services;

use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'password'   => $data['password'],
        ]);

        // Assign customer role
        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id);
        }

        // Create customer profile with referral code
        CustomerProfile::create([
            'user_id'       => $user->id,
            'referral_code' => $this->generateReferralCode(),
            'referred_by'   => $this->resolveReferrer($data['referral_code'] ?? null),
        ]);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user'  => new \App\Http\Resources\UserResource($user->load(['customerProfile', 'roles'])),
            'token' => $token,
        ];
    }

    public function login(array $credentials): ?array
    {
        $email = $credentials['email'];
        $throttleKey = 'login:' . Str::lower($email);

        // Rate limiting: 5 attempts per 15 minutes
        $maxAttempts = config('nexrun.auth.max_login_attempts', 5);
        $lockoutMinutes = config('nexrun.auth.lockout_duration', 15);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return null;
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, $lockoutMinutes * 60);
            return null;
        }

        if ($user->status !== 'active') {
            return null;
        }

        RateLimiter::clear($throttleKey);

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Revoke old tokens and create new one
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        // Audit log
        AuditLog::create([
            'user_id'     => $user->id,
            'module_name' => 'Auth',
            'action_type' => 'login',
            'entity_name' => 'users',
            'entity_id'   => $user->id,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

        return [
            'user'  => new \App\Http\Resources\UserResource($user->load(['customerProfile', 'roles'])),
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();

        AuditLog::create([
            'user_id'     => $user->id,
            'module_name' => 'Auth',
            'action_type' => 'logout',
            'entity_name' => 'users',
            'entity_id'   => $user->id,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    public function refreshToken(User $user): array
    {
        $user->currentAccessToken()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return ['token' => $token];
    }

    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (CustomerProfile::where('referral_code', $code)->exists());

        return $code;
    }

    private function resolveReferrer(?string $referralCode): ?int
    {
        if (!$referralCode) return null;

        $profile = CustomerProfile::where('referral_code', $referralCode)->first();
        return $profile?->user_id;
    }
}

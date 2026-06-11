<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpController extends ApiController
{
    public function send(Request $request): JsonResponse
    {
        $request->validate(['phone' => 'required|string|max:20']);

        $phone = $request->phone;
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = config('nexrun.auth.otp_expiry', 5);

        Cache::put("otp:{$phone}", $otp, now()->addMinutes($expiry));

        // In production: dispatch SMS job
        // For dev: log it
        if (config('nexrun.sms.provider') === 'log') {
            \Log::info("OTP for {$phone}: {$otp}");
        }

        return $this->success(
            ['expires_in' => $expiry * 60],
            'OTP sent successfully.'
        );
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'otp'   => 'required|string|size:6',
        ]);

        $phone = $request->phone;
        $storedOtp = Cache::get("otp:{$phone}");

        if (!$storedOtp || $storedOtp !== $request->otp) {
            return $this->error('Invalid or expired OTP.', 422);
        }

        Cache::forget("otp:{$phone}");

        $user = User::where('phone', $phone)->first();

        if ($user) {
            $user->update([
                'phone_verified_at' => now(),
                'last_login_at'     => now(),
            ]);
        } else {
            $user = User::create([
                'first_name'        => 'User',
                'last_name'         => Str::random(4),
                'email'             => $phone . '@otp.nexrun.local',
                'phone'             => $phone,
                'phone_verified_at' => now(),
                'last_login_at'     => now(),
            ]);

            $customerRole = \App\Models\Role::where('slug', 'customer')->first();
            if ($customerRole) $user->roles()->attach($customerRole->id);

            \App\Models\CustomerProfile::create([
                'user_id'       => $user->id,
                'referral_code' => strtoupper(Str::random(8)),
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user'  => new \App\Http\Resources\UserResource($user->load(['customerProfile', 'roles'])),
            'token' => $token,
        ], 'OTP verified successfully.');
    }
}

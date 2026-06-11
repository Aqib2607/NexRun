<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EmailVerificationController extends ApiController
{
    public function verify(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = $request->user();
        $key = "email_verification:{$user->id}";
        $storedCode = Cache::get($key);

        if (!$storedCode || $storedCode !== $request->code) {
            return $this->error('Invalid verification code.', 422);
        }

        $user->update(['email_verified_at' => now()]);
        Cache::forget($key);

        return $this->success(null, 'Email verified successfully.');
    }

    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return $this->error('Email already verified.', 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("email_verification:{$user->id}", $code, now()->addMinutes(30));

        \Log::info("Email verification code for {$user->email}: {$code}");

        return $this->success(null, 'Verification code sent.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends ApiController
{
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // In production: send email with reset link
        $resetUrl = config('nexrun.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($request->email);
        \Log::info("Password reset link for {$request->email}: {$resetUrl}");

        return $this->success(null, 'Password reset link sent.');
    }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|string',
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/',
            ],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return $this->error('Invalid reset token.', 422);
        }

        // Check token expiry (1 hour)
        if (now()->diffInMinutes($record->created_at) > 60) {
            return $this->error('Reset token has expired.', 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => $request->password]);
        $user->tokens()->delete();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return $this->success(null, 'Password reset successful.');
    }
}

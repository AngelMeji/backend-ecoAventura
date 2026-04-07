<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Send a password reset link to the given email.
     * POST /api/password/email
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Override the reset URL so it points to the frontend SPA
        // instead of Laravel's named route (password.reset)
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return "{$frontendUrl}/reset-password?token={$token}&email=" . urlencode($user->email);
        });

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Se ha enviado el enlace de restablecimiento de contraseña a tu correo electrónico.',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'No pudimos encontrar un usuario con ese correo electrónico.',
            'errors'  => ['email' => [__($status)]],
        ], 422);
    }

    /**
     * Reset the password using the given token.
     * POST /api/password/reset
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Tu contraseña ha sido restablecida correctamente.',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'No se pudo restablecer la contraseña.',
            'errors'  => ['email' => [__($status)]],
        ], 422);
    }
}

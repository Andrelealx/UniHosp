<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{token: string, user: \App\Models\User}
     */
    public function login(array $credentials, string $deviceName, ?string $ipAddress = null): array
    {
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Usuário inativo. Contate um administrador.'],
            ]);
        }

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ])->save();

        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'token' => $token,
            'user' => $user->fresh(),
        ];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
            return;
        }

        $user->tokens()->delete();
    }
}

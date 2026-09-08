<?php

namespace App\Services\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
class AuthenticationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function login(
        string $email,
        string $password
    ): array {
        $user = User::where('email', $email)->first();

        if (
            ! $user ||
            ! password_verify($password, $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials are incorrect.',
                ],
            ]);
        }

        $token = $user->createToken(
            'inventmap-api'
        )->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}

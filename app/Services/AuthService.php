<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param array $credentials
     *
     * @throws AuthenticationException
     * @throws ModelNotFoundException
     *
     * @return array
     */
    public function login(array $credentials): array
    {
        if (!$user = User::query()->where('email', $credentials['email'])->first()) {
            throw (new ModelNotFoundException())->setModel('User', $credentials['email']);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        return [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ];
    }
}

<?php

namespace App\Function;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Login
{
    public static function loginUser(string $email, string $password, int $ROLE)
    {
        $user = User::where("email", $email)->first();

        if (!$user || !Hash::check($password, $user->password) || $user->user_role != $ROLE) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة'],
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];

    }
}


<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    public static function createUserWithRole(array $data, string $roleName = 'User')
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_role' => $roleName === 'admin' ? 1 : 2,
            ]);

            $role = Role::where('role_name', $roleName)->first();

            if ($role) {
                $user->user_roles()->attach($role->id);
            }

            $token = $user->createToken("{$roleName}-token")->plainTextToken;

            DB::commit();

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}


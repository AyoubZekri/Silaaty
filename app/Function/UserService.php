<?php

namespace App\Function;

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
                'phone_number' => $data['phone_number'],
                'family_name' => $data['family_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_role' => $roleName === 'admin' ? 1 : 2,
            ]);

            $role = Role::where('role_name', $roleName)->first();

            if ($role) {
                $user->user_roles()->attach($role->id);
            }

            DB::commit();

            return [
                'user' => $user,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}


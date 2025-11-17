<?php

namespace App\Function;

use App\Mail\confermemail;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public static function createUserWithRole(array $data, string $roleName = 'User')
    {
        DB::beginTransaction();

        try {
            $statusCode = random_int(10000, 99999);
            $user = User::create([
                'name' => $data['name'],
                'phone_number' => $data['phone_number'],
                'family_name' => $data['family_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_role' => $roleName === 'admin' ? 1 : 2,
                'email_verified' => $statusCode,
                'Status' => 2,
                'date_experiment' => now()->addMonth(),
            ]);

            $role = Role::where('role_name', $roleName)->first();

            if ($role) {
                $user->user_roles()->attach($role->id);
            }

            Mail::to($user->email)->send(new WelcomeMail($user));

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


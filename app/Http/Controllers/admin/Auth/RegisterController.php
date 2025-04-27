<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function Registeradmin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors(),
                ], 422);
            }
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_role' => '1',
            ]);

            $adminRole = Role::where('role_name', 'admin')->first();

            if ($adminRole) {
                $user->user_roles()->attach($adminRole->id);
            }

            $token = $user->createToken('admin-token')->plainTextToken;
            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'admin' => $user,
                "token"=>$token
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء إنشاء حساب الأدمن',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

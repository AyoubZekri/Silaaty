<?php

namespace App\Http\Controllers\User\Auth;

use App\Function\Respons;
use App\Function\UserService;
use App\Http\Controllers\Controller;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function RegisterUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'family_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:12',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
                "role" => 'required',
            ]);

            if ($validator->fails()) {
                if ($request->wantsJson()) {
                    return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
                }
                return back()->withErrors($validator)->withInput();
            }

            $roleMap = [
                2 => 'User',
                3 => 'Dealer',
            ];

            $role = $roleMap[$request->role] ?? 'Convicts';

            $result = UserService::createUserWithRole(
                $request->only(['name', 'email', 'password', 'phone_number', 'family_name']),
                $role
            );

            Zakat::create([
                "user_id" => $result["user"]->id
            ]);

            if ($request->wantsJson()) {
                return Respons::success([
                    "data" => $result["user"],
                ]);
            }

            return redirect()->route('home')->with('success', 'تم إنشاء الحساب بنجاح');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return Respons::error('حدث خطأ أثناء إنشاء حساب المستخدم', 500, $e->getMessage());
            }
            return back()->with('error', 'حدث خطأ أثناء إنشاء الحساب')->withInput();
        }
    }


}


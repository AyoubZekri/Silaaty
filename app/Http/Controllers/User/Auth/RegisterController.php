<?php

namespace App\Http\Controllers\User\Auth;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Services\UserService;
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
            ]);

            if ($validator->fails()) {
                Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $result = UserService::createUserWithRole($request->only(['name', 'email', 'password']), 'admin');

            return Respons::success([
                "data" => $result["User"],
                "token" => $result["token"],
            ]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إنشاء حساب الأدمن', 500, $e->getMessage(), );

        }
    }

}


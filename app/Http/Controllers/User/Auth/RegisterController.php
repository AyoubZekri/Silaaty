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
            ]);

            if ($validator->fails()) {
                    return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }




            $result = UserService::createUserWithRole(
                $request->only(['name', 'email', 'password', 'phone_number', 'family_name']),
                "User"
            );

            Zakat::create([
                "user_id" => $result["user"]->id
            ]);
            

            return Respons::success();


        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إنشاء حساب المستخدم', 500, $e->getMessage());

        }
    }


}


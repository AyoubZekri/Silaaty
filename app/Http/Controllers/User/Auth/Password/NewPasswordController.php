<?php

namespace App\Http\Controllers\User\Auth\Password;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Function\Respons;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class NewPasswordController extends Controller
{
    public function newpassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8|confirmed',
                'email' => 'required|email',

            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return Respons::error('المستخدم غير موجود', 404);
            }


            $user->password = Hash::make($request->password);
            $user->save();

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تغيير كلمة المرور', 500, $e->getMessage());
        }
    }
}

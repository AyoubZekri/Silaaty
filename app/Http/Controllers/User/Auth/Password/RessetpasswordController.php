<?php

namespace App\Http\Controllers\User\Auth\Password;

use App\Http\Controllers\Controller;
use App\Function\Respons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RessetpasswordController extends Controller
{
    public function reset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $user = auth()->user();

            if (!$user) {
                return Respons::error('المستخدم غير مصادق عليه', 401);
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return Respons::error('كلمة المرور القديمة غير صحيحة', 401);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تغيير كلمة المرور', 500, $e->getMessage());
        }
    }
}

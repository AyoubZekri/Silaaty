<?php

namespace App\Http\Controllers\User\Auth\Password;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Function\Respons;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class NewPasswordController extends Controller
{
    public function reset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $user = auth()->user();


            $user->password = Hash::make($request->new_password);
            $user->save();

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تغيير كلمة المرور', 500, $e->getMessage());
        }
    }
}

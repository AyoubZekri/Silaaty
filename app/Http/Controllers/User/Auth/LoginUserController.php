<?php

namespace App\Http\Controllers\User\Auth;

use App\Function\Login;
use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class LoginUserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
                'fcm_token' => 'nullable|string',
            ]);

            $result = Login::loginUser($request->email, $request->password, 2);

            $user = User::where('email', $request->email)->first();

            if ($request->filled('fcm_token')) {
                $user->update([
                    'fcm_token' => $request->fcm_token,
                ]);
            }

            return Respons::success($result, 'تم تسجيل الدخول بنجاح');

        } catch (ValidationException $e) {
            return Respons::error('بيانات الدخول غير صحيحة', 422, $e->errors());
        } catch (Exception $e) {
            return Respons::error('حدث خطأ أثناء محاولة تسجيل الدخول', 500, $e->getMessage());
        }
    }
}

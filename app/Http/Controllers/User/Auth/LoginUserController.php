<?php

namespace App\Http\Controllers\User\Auth;

use App\Function\Login;
use App\Function\Respons;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Exception;

class LoginUserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $result = Login::loginUser($request->email, $request->password, 2);

            return Respons::success($result, 'تم تسجيل الدخول بنجاح');

        } catch (ValidationException $e) {
            return Respons::error('بيانات الدخول غير صحيحة', 422, $e->errors());
        } catch (Exception $e) {
            return Respons::error('حدث خطأ أثناء محاولة تسجيل الدخول', 500, $e->getMessage());
        }
    }
}

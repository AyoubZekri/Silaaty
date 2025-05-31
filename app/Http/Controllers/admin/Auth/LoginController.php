<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Function\Login;
use App\Function\Respons;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;
class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $result = Login::loginUser($request->email, $request->password, 1);

            return Respons::success($result, 'تم تسجيل الدخول بنجاح');

        } catch (ValidationException $e) {
            return Respons::error('بيانات الدخول غير صحيحة', 422, $e->errors());
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء محاولة تسجيل الدخول', 500, $e->getMessage());
        }
    }
}

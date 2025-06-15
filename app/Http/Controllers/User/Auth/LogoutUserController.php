<?php

namespace App\Http\Controllers\User\Auth;

use App\Function\Logout_user;
use App\Function\Respons;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutUserController extends Controller
{
    public function logout(Request $request)
    {
        try {
            Logout_user::Logout_user($request);

            return Respons::success();

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تسجيل الخروج', 500, $e->getMessage());
        }

    }
}

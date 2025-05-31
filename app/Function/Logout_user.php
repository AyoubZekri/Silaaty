<?php
namespace App\Function;
use App\Function\Respons;
use Exception;

class Logout_user
{
    public static function Logout_user($request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return Respons::success();
        } catch (Exception $e) {
            return Respons::error('حدث خطأ أثناء تسجيل الخروج', 500, $e->getMessage());
        }
    }
}

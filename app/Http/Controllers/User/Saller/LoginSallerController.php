<?php

namespace App\Http\Controllers\User\Saller;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginSallerController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $seller = Seller::with('user')->where('email', $request->email)->first();

            if (!$seller || !Hash::check($request->password, $seller->password)) {
                return Respons::error('البريد الإلكتروني أو كلمة المرور غير صحيحة', 401);
            }
            $token = $seller->user->createToken('api_token')->plainTextToken;
            $seller->token = $token;

            return Respons::success(['sellerData' => $seller, 'token' => $token]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تسجيل الدخول', 500, $e->getMessage());
        }
    }
}

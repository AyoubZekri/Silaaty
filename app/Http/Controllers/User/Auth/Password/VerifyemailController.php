<?php

namespace App\Http\Controllers\User\Auth\Password;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifyemailController extends Controller
{
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        $user = auth()->user();


        if ($user->email_verification_code != $request->code) {
            return Respons::error('رمز التحقق غير صحيح', 401);
        }

        $user->email_verified = null;
        $user->save();

        return Respons::success();
    }
}

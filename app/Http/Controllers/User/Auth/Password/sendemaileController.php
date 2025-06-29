<?php

namespace App\Http\Controllers\User\Auth\Password;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Mail\confermemail;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class sendemaileController extends Controller
{
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());

        }

        $code = random_int(1000, 9999);

        $user = auth()->user();

        $user->email_verified = $code;
        $user->save();

        Mail::to($user->email)->send(new confermemail($code));

        return Respons::success();

    }
}

<?php

namespace App\Http\Controllers\User\Auth;

use App\Function\Respons;
use App\Function\UserService;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{

    public function RegisterUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'family_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:12',
                'email' => 'required|email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                if ($existingUser->Status == 0) {
                    $existingUser->delete();
                } else {
                    return Respons::error('البريد الإلكتروني مستخدم مسبقاً', 422, [
                        'البريد الإلكتروني مستخدم مسبقاً'
                    ]);
                }
            }

            $result = UserService::createUserWithRole(
                $request->only(['name', 'email', 'password', 'phone_number', 'family_name']),
                "User"
            );
            $nisab = Zakat::first()?->zakat_nisab;


            Zakat::create([
                "user_id" => $result["user"]->id,
                "zakat_nisab" => $nisab,
                "uuid"        => Str::uuid(),
            ]);


            return Respons::success();


        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إنشاء حساب المستخدم', 500, $e->getMessage());

        }
    }

    public function getuser()
    {

        try {
            $user = User::where("id", auth()->id())->get();
            return Respons::success(['data' => $user]);
        } catch (\Exception $th) {
            return Respons::error('المستخدم غير موجودة', 404);
        }

    }




}


<?php

namespace App\Http\Controllers\Doctor\Auth;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                "message" => "البيانات فارغة",
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password) || $user->user_role != 4) {
                return response()->json([
                    'status' => 0,
                    'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة أو ليس لديك صلاحية الدخول.',
                ], 401);
            }

            // if (!$user->email_verified) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'يجب تأكيد البريد الإلكتروني أولاً.',
            //     ], 403);
            // }

            $Doctor = Doctor::where('user_id', $user->id)
                ->with(['schedules'])
                ->with(['clinic:id,name', 'specialty:id,name'])
                ->first();

            if (!$Doctor) {
                return response()->json([
                    'status' => 0,
                    'message' => 'لم يتم العثور على بيانات الطبيب.',
                ], 404);
            }


            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'user' => $user,
                'Doctor' => $Doctor,
                'token' => $token,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء محاولة تسجيل الدخول.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

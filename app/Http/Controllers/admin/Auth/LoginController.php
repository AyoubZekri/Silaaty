<?php

namespace App\Http\Controllers\admin\Auth;

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

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password) || $user->user_role != 1) {
                throw ValidationException::withMessages([
                    'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة'],
                ]);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 0,
                'message' => 'بيانات الدخول غير صحيحة',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء محاولة تسجيل الدخول',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

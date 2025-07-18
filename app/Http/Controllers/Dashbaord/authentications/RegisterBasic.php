<?php

namespace App\Http\Controllers\Dashbaord\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;
use Hash;


class RegisterBasic extends Controller
{
    public function index()
    {
        return view('content.authentications.auth-register-basic');
    }

    function csrftoken()
    {
        return csrf_token();
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'family_name' => $request->name,
                'password' => $request->password,
                'user_role' => 1,
            ]);


            return redirect("/");
        } catch (\Throwable $e) {
            // طباعة الخطأ مباشرة
            dd('Exception: ' . $e->getMessage());

            // أو تقدر تسجلها في اللوق بدون توقف التنفيذ:
            // Log::error('Register error: ' . $e->getMessage());
            // return back()->withErrors(['error' => 'فشل إنشاء المستخدم.']);
        }
    }
}

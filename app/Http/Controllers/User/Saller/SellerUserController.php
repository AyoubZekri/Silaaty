<?php

namespace App\Http\Controllers\User\Saller;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SellerUserController extends Controller
{
    public function index()
    {
        try {
            $sellers = User::where('parent_id', auth()->id())->get();
            return Respons::success(['sellers' => $sellers]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب البائعين', 500, $e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $seller = User::create([
                'parent_id' => auth()->id(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_role' => 3, // نوع بائع
                'family_name' => '',
                'phone_number' => '',
            ]);

            return Respons::success(['sellerData' => $seller]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إضافة البائع', 500, $e->getMessage());
        }
    }

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

            $seller = User::with('parent')->where('email', $request->email)->first();
            
            if ($seller && $seller->user_role != 3) {
                return Respons::error('حسابك ليس بائع', 403);
            }

            if (!$seller || !Hash::check($request->password, $seller->password)) {
                return Respons::error('البريد الإلكتروني أو كلمة المرور غير صحيحة', 401);
            }

            $token = $seller->createToken('api_token')->plainTextToken;
            $seller->token = $token;

            return Respons::success(['sellerData' => $seller, 'token' => $token]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تسجيل الدخول', 500, $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $request->id,
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $seller = User::where('parent_id', auth()->id())->findOrFail($request->id);
            
            if ($request->has('name')) $seller->name = $request->name;
            if ($request->has('email')) $seller->email = $request->email;
            
            if ($request->filled('password')) {
                $seller->password = Hash::make($request->password);
            }

            $seller->save();

            return Respons::success(['sellerData' => $seller]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تعديل بيانات البائع', 500, $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $seller = User::where('parent_id', auth()->id())->findOrFail($request->id);
            $seller->delete();

            return Respons::success(['message' => 'تم حذف البائع بنجاح']);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء حذف البائع', 500, $e->getMessage());
        }
    }
}

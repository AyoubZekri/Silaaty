<?php

namespace App\Http\Controllers\User\Saller;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddSallerController extends Controller
{
    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|unique:sellers,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $seller = Seller::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return Respons::success(['sellerData' => $seller]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إضافة البائع', 500, $e->getMessage());
        }
    }
}

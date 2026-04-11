<?php

namespace App\Http\Controllers\User\Saller;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateSallerController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:sellers,id',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|unique:sellers,email,'.$request->id,
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $seller = Seller::findOrFail($request->id);
            $seller->name = $request->name ?? $seller->name;
            $seller->phone = $request->phone ?? $seller->phone;
            $seller->email = $request->email ?? $seller->email;
            
            if ($request->filled('password')) {
                $seller->password = $request->password;
            }

            $seller->save();

            return Respons::success(['sellerData' => $seller]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تعديل بيانات البائع', 500, $e->getMessage());
        }
    }
}

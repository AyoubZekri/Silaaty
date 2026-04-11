<?php

namespace App\Http\Controllers\User\Saller;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteSallerController extends Controller
{
    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:sellers,id',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $seller = Seller::findOrFail($request->id);
            $seller->delete();

            return Respons::success(['message' => 'تم حذف البائع بنجاح']);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء حذف البائع', 500, $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\User\Product;

use App\Function\Respons;
use App\Function\Zakats;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class DeleteProductController extends Controller
{

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $product = Product::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if ($product->Product_image && Storage::disk('public')->exists($product->Product_image)) {
                Storage::disk('public')->delete($product->Product_image);
            }

            $product->delete();

            Zakats::Zakats();


            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء الحذف', 500, $e->getMessage());
        }
    }

}

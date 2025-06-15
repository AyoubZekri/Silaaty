<?php

namespace App\Http\Controllers\User\Product;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchProductController extends Controller
{
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $query = $request->input('query');

            $products = Product::where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('product_name', 'LIKE', "%$query%");
                })
                ->get();

            return Respons::success(['data' => $products]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء البحث', 500, $e->getMessage());
        }
    }

}

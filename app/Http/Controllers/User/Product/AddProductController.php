<?php

namespace App\Http\Controllers\User\Product;

use App\Function\Respons;
use App\Function\Zakats;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddProductController extends Controller
{
    public function AddProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "categoris_id"=>"required",
                'categorie_id' => 'required',
                'product_name' => 'required|string|max:255',
                'product_description' => 'nullable|string',
                'product_quantity' => 'required|numeric|min:1',
                'product_price' => 'required|numeric|min:0',
                'product_price_total' => 'required|numeric|min:0',
                'product_debtor_Name' => 'nullable|string|max:255',
                'product_payment' => 'nullable|numeric|min:0',
                'product_debtor_phone' => 'nullable|string|max:20',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }


            $data = $request->all();
            $data['user_id'] = auth()->id();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $data['Product_image'] = $path;
            }

            $prodact = Product::create($data);

            Zakats::Zakats();

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إضافة المنتج', 500, $e->getMessage());
        }
    }

}

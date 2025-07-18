<?php

namespace App\Http\Controllers\User\Product;

use App\Function\Respons;
use App\Function\Zakats;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditProductController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:products,id',
                "categoris_id" => "sometimes",
                'categorie_id' => 'sometimes',
                'product_name' => 'nullable|string|max:255',
                'product_description' => 'nullable|string',
                'product_quantity' => 'nullable|numeric|min:1',
                'product_price' => 'nullable|numeric|min:0',
                'product_price_purchase' => 'nullable|numeric|min:0',
                'product_price_total_purchase' => 'nullable|numeric|min:0',
                'product_price_total' => 'nullable|numeric|min:0',
                'product_debtor_Name' => 'nullable|string|max:255',
                'product_payment' => 'nullable|numeric|min:0',
                'product_debtor_phone' => 'nullable|string|max:20',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $product = Product::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            $data = $request->only([
                "product_price_total_purchase",
                "product_price_purchase",
                "categoris_id",
                'product_name',
                'product_description',
                'product_quantity',
                'product_price',
                'product_debtor_Name',
                'product_payment',
                'product_debtor_phone',
                'categorie_id',
                'product_price_total',
            ]);


            if ($request->hasFile('image')) {
                if ($product->Product_image && \Storage::disk('public')->exists($product->Product_image)) {
                    \Storage::disk('public')->delete($product->Product_image);
                }

                $path = $request->file('image')->store('products', 'public');
                $data['Product_image'] = $path;
            }

            $product->update($data);

            Zakats::Zakats();

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تحديث المنتج', 500, $e->getMessage());
        }
    }

    public function SwitchProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        try {
            $product = Product::where("user_id", auth()->id())
                ->where("id", $request->id)
                ->firstOrFail();

            $product->categorie_id = $product->categorie_id == 1 ? 2 : 1;
            $product->save();

            return Respons::success('تم تغيير الفئة بنجاح');

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تحديث المنتج', 500, $e->getMessage());
        }
    }


}


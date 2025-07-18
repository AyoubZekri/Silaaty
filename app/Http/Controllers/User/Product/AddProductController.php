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
            $allData = $request->all();

            $isMultiple = isset($allData['products']) && is_array($allData['products']);

            $products = $isMultiple ? $allData['products'] : [$allData];

            if (count($products) === 0) {
                return Respons::error('لا توجد بيانات لإضافة منتجات', 422);
            }

            foreach ($products as $index => $productData) {
                $validator = Validator::make($productData, [
                    "categoris_id" => "sometimes",
                    'categorie_id' => "required",
                    'invoies_id' => "sometimes",
                    'product_name' => 'required|string|max:255',
                    'product_description' => 'nullable|string',
                    'product_quantity' => 'nullable|numeric|min:1',
                    'product_price' => 'nullable|numeric',
                    'product_price_total_purchase' => 'nullable|numeric',
                    'product_price_purchase' => 'nullable|numeric',
                    'product_price_total' => 'nullable|numeric',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);


                if ($validator->fails()) {
                    return Respons::error("خطأ في المنتج رقم " . ($index + 1), 422, $validator->errors());
                }

                $productData['user_id'] = auth()->id();

                if ($request->hasFile($isMultiple ? "products.$index.image" : "image")) {
                    $file = $isMultiple ? $request->file("products")[$index]['image'] : $request->file("image");
                    $path = $file->store('products', 'public');
                    $productData['Product_image'] = $path;
                }

                Product::create($productData);
            }

            Zakats::Zakats();

            return Respons::success('تمت إضافة المنتج' . (count($products) > 1 ? 'ات' : '') . ' بنجاح');
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إضافة المنتجات', 500, $e->getMessage());
        }
    }

}

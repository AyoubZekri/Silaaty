<?php

namespace App\Http\Controllers\User\Product;

use App\Function\Notification;
use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\categories;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShwoProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::where('user_id', auth()->id())->get();

            $products->map(function ($product) {
                $product->Product_image = $product->Product_image
                    ? asset('storage/' . $product->Product_image)
                    : null;
            });

            return Respons::success(['data' => $products]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب المنتجات', 500, $e->getMessage());
        }
    }


    public function ShowProdact(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Categoris_id' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $products = Product::where('user_id', auth()->id())->Where("categorie_id", $request->Categoris_id)->get();

            // $products->map(function ($product) {
            //     $product->Product_image = $product->Product_image
            //         ? asset('storage/' . $product->Product_image)
            //         : null;
            // });

            // $notification = new Notification();
            // $result = $notification->sendNotificationToTopic('users', 'تنبيه جديد', 'هذا إشعار جماعي');


            return Respons::success(['data' => $products]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب المنتجات', 500, $e->getMessage());
        }
    }

    public function ShowProdactbyCat(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Categorie_id' => 'required',
            'Categoris_id' => "sometimes",
        ]);

        try {

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $query = Product::where('user_id', auth()->id())
                ->where('categorie_id', $request->Categorie_id);

            if ($request->filled('Categoris_id')) {
                $query->where('categoris_id', $request->Categoris_id);
            }

            $products = $query->get();

            // $products->map(function ($product) {
            //     $product->Product_image = $product->Product_image
            //         ? asset('storage/' . $product->Product_image)
            //         : null;
            // });
            // $notification = new Notification();
            // $result = $notification->sendNotificationToTopic('users', 'تنبيه جديد', 'هذا إشعار جماعي');
            // $notification->sendNotification("dP_AsfpnT1-GoGco5b268F:APA91bHfezM-OidzCvO1EUcl3DOpr2VQgEJx3sDGKwt5b7IBCnc8ZUr5se69C9201OULbfNCCpNnatDmS-b98q5LhNnDGsOGcrL1NMPvojXXxAma1a2IbKE", "hi", "hi", auth()->id());

            return Respons::success(['data' => $products]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب المنتجات', 500, $e->getMessage());
        }
    }

    public function ShowProducts_zakat(Request $request)
    {

        try {

            $products = Product::where('user_id', auth()->id())
                ->where('categorie_id', 1)
                ->get();
            $TotalPrise = Product::where('user_id', auth()->id())
                ->where('categorie_id', 1)
                ->sum('product_price_total');

            $TotalPriseDealer = Product::where('user_id', auth()->id())
                ->where('categorie_id', 3)
                ->sum('product_price_total');

            $TotalPriseConvicts = Product::where('user_id', auth()->id())
                ->where('categorie_id', 4)
                ->sum('product_price_total');

            $products->map(function ($product) {
                $product->Product_image = $product->Product_image
                    ? asset('storage/' . $product->Product_image)
                    : null;
            });

            return Respons::success(['data' => $products, "SumTotalPrise" => $TotalPrise, "SumTotalDealer" => $TotalPriseDealer, "SumTotalPriseConvicts" => $TotalPriseConvicts,]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب المنتجات', 500, $e->getMessage());
        }
    }


    public function show(Request $request)
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

            // $product->Product_image = $product->Product_image
            //     ? asset('storage/' . $product->Product_image)
            //     : null;

            return Respons::success(['data' => $product]);
        } catch (\Exception $e) {
            return Respons::error('المنتج غير موجود أو غير مسموح الوصول إليه', 404);
        }
    }



}

<?php

namespace App\Http\Controllers\User\Categorie;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Categoris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShowCategorisController extends Controller
{
    public function index()
    {
        try {

            $categories = Categoris::where("user_id",auth()->id())->get();

            return Respons::success(['catdata' => $categories]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الفئات', 500, $e->getMessage());
        }
    }

    public function show(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id' => "required",
            ]);


            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }


            $category = Categoris::findOrFail($request->id);

            $category->categoris_image = $category->categoris_image
                ? asset('storage/' . $category->categoris_image)
                : null;

            return Respons::success(['data' => $category]);
        } catch (\Exception $e) {
            return Respons::error('الفئة غير موجودة', 404);
        }
    }
}

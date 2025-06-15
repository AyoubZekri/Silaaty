<?php

namespace App\Http\Controllers\User\Categorie;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddCategorisController extends Controller
{
    public static function AddCategoris(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'categorie_name' => 'required|string|max:255',
                'categorie_name_ar' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $data = [
                'categorie_name' => $request->categorie_name,
                'categorie_name_ar' => $request->categorie_name_ar,
            ];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('categories', 'public');
                $data['categories_image'] = $path;
            }

            $category = categories::create($data);

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إنشاء حساب الفئة', 500, $e->getMessage(), );
        }

    }
}

<?php

namespace App\Http\Controllers\User\Categorie;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Categoris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddCategorisController extends Controller
{
    public static function AddCategoris(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'categorie_name' => 'required|string|max:255',
                'categorie_name_fr' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }
            $data = $request->all();

            $data = [
                'categoris_name' => $request->categorie_name,
                'categoris_name_fr' => $request->categorie_name_fr,
                'user_id'=>auth()->id(),
            ];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('categories', 'public');
                $data['categoris_image'] = $path;
            }

            $category = Categoris::create($data);

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إنشاء حساب الفئة', 500, $e->getMessage(), );
        }

    }
}

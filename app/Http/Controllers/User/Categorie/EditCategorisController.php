<?php

namespace App\Http\Controllers\User\Categorie;

use App\Http\Controllers\Controller;
use App\Models\Categoris;
use Illuminate\Http\Request;
use App\Function\Respons;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EditCategorisController extends Controller
{
    public function updateCategoris(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id'=>"required",
                'categorie_name' => 'sometimes|string|max:255',
                'categorie_name_fr' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $category = Categoris::findOrFail($request->id);


            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            if ($request->has('categorie_name')) {
                $category->categoris_name = $request->categorie_name;
            }

            if ($request->has('categorie_name')) {
                $category->categoris_name_fr = $request->categorie_name_ar;
            }

            if ($request->hasFile('image')) {
                if ($category->categoris_image) {
                    Storage::disk('public')->delete($category->categoris_image);
                }

                $category->categoris_image = $request->file('image')->store('categories', 'public');
            }

            $category->save();

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تحديث الفئة', 500, $e->getMessage());
        }
    }
}

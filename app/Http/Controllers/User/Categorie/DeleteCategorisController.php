<?php

namespace App\Http\Controllers\User\Categorie;


use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeleteCategorisController extends Controller
{
    public function destroyCategoris(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'=>"required",
            ]);

            $category = categories::findOrFail($request->id);

            if ($category->categories_image) {
                Storage::disk('public')->delete($category->categories_image);
            }

            $category->delete();

            return Respons::success();
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء حذف الفئة', 500, $e->getMessage());
        }
    }
}

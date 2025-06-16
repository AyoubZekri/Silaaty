<?php

namespace App\Http\Controllers\User\Invoies;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\invoies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteInvoiesController extends Controller
{
    public function deleteInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|exists:invoies,id",
        ]);

        if ($validator->fails()) {
            return Respons::error("خطأ في البيانات", 422, $validator->errors());
        }

        try {
            $invoice = invoies::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$invoice) {
                return Respons::error('الفاتورة غير موجودة أو لا تملك صلاحية حذفها', 404);
            }

            $invoice->delete();

            return Respons::success('تم حذف الفاتورة بنجاح');
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء حذف الفاتورة', 500, $e->getMessage());
        }
    }

}

<?php

namespace App\Http\Controllers\User\Invoies;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\invoies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditInvoiesController extends Controller
{
    public function updateInvoice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id"=>"required",
                "invoies_payment_date" => "sometimes|date",
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $invoice = invoies::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$invoice) {
                return Respons::error('الفاتورة غير موجودة', 404);
            }

            $invoice->update($request->only([
                'invoies_payment_date',
            ]));

            return Respons::success('تم تعديل الفاتورة بنجاح');
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تعديل الفاتورة', 500, $e->getMessage());
        }
    }

    public function updateInvoiceSwitch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id"=>"required",
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $invoice = invoies::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if (!$invoice) {
                return Respons::error('الفاتورة غير موجودة', 404);
            }

           $invoice->invoies_status = $invoice->invoies_status == 1 ? 2 : 1;
           $invoice->save();


            return Respons::success('تم تعديل الفاتورة بنجاح');
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تعديل الفاتورة', 500, $e->getMessage());
        }
    }


}

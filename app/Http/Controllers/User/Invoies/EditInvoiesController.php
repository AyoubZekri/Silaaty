<?php

namespace App\Http\Controllers\User\Invoies;

use App\Function\Respons;
use App\Function\Zakats;
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
                "Payment_price" => "sometimes",

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



            if ($request->filled('Payment_price')) {
                $newPayment = $invoice->Payment_price + $request->Payment_price;
                $invoice->Payment_price = $newPayment;
            }

            if ($request->filled('invoies_payment_date')) {
                $invoice->invoies_payment_date = $request->invoies_payment_date;
            }

            $invoice->save();
            Zakats::Zakats();

            return Respons::success('تم تعديل الفاتورة بنجاح');
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تعديل الفاتورة', 500, $e->getMessage());
        }
    }



}

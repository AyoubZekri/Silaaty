<?php

namespace App\Http\Controllers\User\Invoies;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\invoies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddInvoiesController extends Controller
{
    public function addInvoice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "Transaction_id" => "required",
                "invoies_payment_date" => "sometimes|date",
                "Payment_price" => "sometimes",
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $invoiceCount = invoies::where("user_id", auth()->id())
                ->where("Transaction_id", $request->Transaction_id)
                ->max('invoies_numper');

            $invoiceData = $request->only([
                "Transaction_id",
                "invoies_payment_date",
                "Payment_price"
            ]);

            $invoiceData['user_id'] = auth()->id();
            $invoiceData['invoies_numper'] = $invoiceCount + 1;
            $invoiceData['invoies_date'] = Carbon::now();

            invoies::create($invoiceData);

            return Respons::success('تمت إضافة الفاتورة بنجاح');
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء إضافة الفاتورة', 500, $e->getMessage());
        }
    }
}

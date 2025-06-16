<?php

namespace App\Http\Controllers\User\Invoies;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\invoies;
use App\Models\Product;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShwoInvoiesController extends Controller
{
    public function getMyInvoicesByTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "transaction_id" => "required",
        ]);

        if ($validator->fails()) {
            return Respons::error("خطأ في البيانات", 422, $validator->errors());
        }

        try {
            $invoices = invoies::where('user_id', auth()->id())
                ->where('Transaction_id', $request->transaction_id)
                ->get();

            if ($invoices->isEmpty()) {
                return Respons::error('لا توجد فواتير لهذه المعاملة', 404);
            }

            $transaction = Transactions::where('user_id', auth()->id())
                ->where('id', $request->transaction_id)
                ->first();

            if (!$transaction) {
                return Respons::error('المعاملة غير موجودة', 404);
            }

            $invoiceIds = $invoices->pluck('id');

            $isPurchase = $transaction->transactions == 1;

            $sumPrice = Product::where('user_id', auth()->id())
                ->whereIn('invoice_id', $invoiceIds)
                ->sum($isPurchase ? 'product_price_total_purchase' : 'product_price_total');

            return Respons::success([
                'invoices' => $invoices,
                'transaction' => $transaction,
                'sum_price' => $sumPrice
            ]);

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الفواتير', 500, $e->getMessage());
        }
    }


    public function showInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);

        if ($validator->fails()) {
            return Respons::error("خطأ في البيانات", 422, $validator->errors());
        }

        try {
            $invoice = Invoies::where('user_id', auth()->id())
                ->where('id', $request->id)
                ->first();

            if (!$invoice) {
                return Respons::error('الفاتورة غير موجودة', 404);
            }

            $transaction = Transactions::where('user_id', auth()->id())
                ->where('id', $invoice->Transaction_id)
                ->first();

            if (!$transaction) {
                return Respons::error('المعاملة غير موجودة لهذه الفاتورة', 404);
            }

            $isPurchase = $transaction->transactions == 1;

            $sumPrice = Product::where('user_id', auth()->id())
                ->where('invoice_id', $invoice->id)
                ->sum($isPurchase ? 'product_price_total_purchase' : 'product_price_total');

            return Respons::success([
                'invoice' => $invoice,
                'transaction' => $transaction,
                'sum_price' => $sumPrice
            ]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الفاتورة', 500, $e->getMessage());
        }
    }

}

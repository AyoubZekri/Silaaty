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

            $sumpaymentPrice = invoies::where('user_id', auth()->id())
                ->where('Transaction_id', $request->transaction_id)
                ->sum("payment_Price");



            $transaction = Transactions::where('user_id', auth()->id())
                ->where('id', $request->transaction_id)
                ->first();

            if (!$transaction) {
                return Respons::error('المعاملة غير موجودة', 404);
            }

            $isPurchase = $transaction->transactions == 1;

            $sumPrice = 0;

            $invoices = $invoices->map(function ($invoice) use ($isPurchase, &$sumPrice) {
                $invoiceSum = Product::where('user_id', auth()->id())
                    ->where('invoies_id', $invoice->id)
                    ->sum($isPurchase ? 'product_price_total_purchase' : 'product_price_total');

                $invoice->invoice_sum = $invoiceSum;

                $sumPrice += $invoiceSum;

                return $invoice;
            });

            return Respons::success([
                'invoices' => $invoices,
                'transaction' => $transaction,
                'sum_price' => $sumPrice,
                'sum_payment_Price' => $sumpaymentPrice,
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
                ->where('invoies_id', $invoice->id)
                ->sum($isPurchase ? 'product_price_total_purchase' : 'product_price_total');
            $Product = Product::where('user_id', auth()->id())
                ->where('invoies_id', $invoice->id)
                ->get();

            return Respons::success([
                'invoice' => $invoice,
                'transaction' => $transaction,
                'Product' => $Product,
                'sum_price' => $sumPrice
            ]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الفاتورة', 500, $e->getMessage());
        }
    }

}

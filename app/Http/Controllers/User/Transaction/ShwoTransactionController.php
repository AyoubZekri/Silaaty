<?php

namespace App\Http\Controllers\User\Transaction;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\invoies;
use App\Models\Product;
use App\Models\Transactions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShwoTransactionController extends Controller
{
    public function getByType(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transactions' => 'required',
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $transactions = Transactions::where('user_id', auth()->id())
                ->where('transactions', $request->transactions)
                ->get();
            $invoices = invoies::where('user_id', auth()->id())
                ->where('Transaction_id', $transactions->id)
                ->get();

            if ($transactions->isEmpty()) {
                return Respons::error('لا توجد معاملات من هذا النوع', 404);
            }

            $invoiceIds = $invoices->pluck('id');

            $isPurchase = $request->transactions == 1;

            $sumPrice = Product::where('user_id', auth()->id())
                ->whereIn('invoice_id', $invoiceIds)
                ->sum($isPurchase ? 'product_price_total_purchase' : 'product_price_total');


            return Respons::success([
                'transaction' => $transactions,
                'sum_price' => $sumPrice
            ]);
        } catch (Exception $e) {
            return Respons::error('فشل في جلب المعاملات', 500, $e->getMessage());
        }
    }
}

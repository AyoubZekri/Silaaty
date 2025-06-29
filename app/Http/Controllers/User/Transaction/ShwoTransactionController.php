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

            $isPurchase = $request->transactions == 1;

            $transactions = Transactions::where('user_id', auth()->id())
                ->where('transactions', $request->transactions)
                ->get();

            if ($transactions->isEmpty()) {
                return Respons::error('لا توجد معاملات من هذا النوع', 404);
            }

            $result = $transactions->map(function ($transaction) use ($isPurchase) {
                $invoiceIds = invoies::where('user_id', auth()->id())
                    ->where('Transaction_id', $transaction->id)
                    ->pluck('id');

                $sumPrice =Product::where('user_id', auth()->id())
                    ->whereIn('invoies_id', $invoiceIds)
                    ->sum($isPurchase ? 'product_price_total_purchase' : 'product_price_total');

                return [
                    'transaction' => $transaction,
                    "sum_price" => round((float) $sumPrice, 2)
                ];
            });

            return Respons::success($result);
        } catch (Exception $e) {
            return Respons::error('فشل في جلب المعاملات', 500, $e->getMessage());
        }
    }
}

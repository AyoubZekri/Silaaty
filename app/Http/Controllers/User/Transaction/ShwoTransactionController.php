<?php

namespace App\Http\Controllers\User\Transaction;

use App\Function\Respons;
use App\Function\Zakats;
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
                'query' => 'nullable|string|max:255',

            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $isPurchase = $request->transactions == 1;
            $query = $request->input('query');


            $transactions = Transactions::where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%$query%");
                    $q->orWhere('family_name', 'LIKE', "%$query%");
                })
                ->where('transactions', $request->transactions)
                ->get();

            $result = $transactions->map(function ($transaction) use ($isPurchase) {
                $invoiceIds = invoies::where('user_id', auth()->id())
                    ->where('Transaction_id', $transaction->id)
                    ->pluck('id');

                $sumPrice = Product::where('user_id', auth()->id())
                    ->whereIn('invoies_id', $invoiceIds)
                    ->sum($isPurchase ? 'product_price_total_purchase' : 'product_price_total');

                $paidAmount = invoies::where('user_id', auth()->id())
                    ->whereIn('id', $invoiceIds)
                    ->sum('Payment_price');

                $sumPrice = $sumPrice - $paidAmount;

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



    public function Switch(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transactions::where('id', $request->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$transaction) {
            return Respons::error('المعاملة غير موجودة  ', 404);
        }


        $transaction->Status = $transaction->Status == 0 ? 1 : 0;


        $transaction->save();

        Zakats::Zakats();


        return response()->json(['status' => 1, 'message' => 'تم تغير الحالة']);
    }

}

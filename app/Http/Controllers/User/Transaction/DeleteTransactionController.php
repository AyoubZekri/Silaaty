<?php

namespace App\Http\Controllers\User\Transaction;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteTransactionController extends Controller
{
    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:transactions,id',
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $transaction = Transactions::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$transaction) {
                return Respons::error('المعاملة غير موجودة أو لا تملك صلاحية حذفها', 404);
            }

            $transaction->delete();

            return Respons::success('تم حذف المعاملة بنجاح');
        } catch (Exception $e) {
            return Respons::error('فشل في حذف المعاملة', 500, $e->getMessage());
        }
    }
}

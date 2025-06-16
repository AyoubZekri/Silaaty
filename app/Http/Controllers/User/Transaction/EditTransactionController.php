<?php

namespace App\Http\Controllers\User\Transaction;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditTransactionController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:transactions,id',
                'name' => 'sometimes|string',
                'family_name' => 'nullable|string',
                'phone_number' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $transaction = Transactions::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$transaction) {
                return Respons::error('المعاملة غير موجودة أو لا تملك صلاحية تعديلها', 404);
            }

            $transaction->update($request->only(['name', 'family_name', 'phone_number']));

            return Respons::success('تم تعديل المعاملة بنجاح', $transaction);
        } catch (Exception $e) {
            return Respons::error('فشل في تعديل المعاملة', 500, $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\User\Transaction;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddTransactionController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transactions' => 'required|in:1,2',
                'name' => 'required|string',
                'family_name' => 'nullable|string',
                'phone_number' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $data = $request->only(['transactions', 'name', 'family_name', 'phone_number']);
            $data['user_id'] = auth()->id();

            $transaction = Transactions::create($data);

            return Respons::success('تم إنشاء المعاملة بنجاح', $transaction);
        } catch (Exception $e) {
            return Respons::error('فشل في إنشاء المعاملة', 500, $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:transactions,id',
                'transactions' => 'sometimes|in:1,2',
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

            $transaction->update($request->only(['transactions', 'name', 'family_name', 'phone_number']));

            return Respons::success('تم تعديل المعاملة بنجاح', $transaction);
        } catch (Exception $e) {
            return Respons::error('فشل في تعديل المعاملة', 500, $e->getMessage());
        }
    }

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

    public function getByType(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transactions' => 'required|in:1,2',
            ]);

            if ($validator->fails()) {
                return Respons::error("خطأ في البيانات", 422, $validator->errors());
            }

            $transactions = Transactions::where('user_id', auth()->id())
                ->where('transactions', $request->transactions)
                ->get();

            if ($transactions->isEmpty()) {
                return Respons::error('لا توجد معاملات من هذا النوع', 404);
            }

            return Respons::success($transactions);
        } catch (Exception $e) {
            return Respons::error('فشل في جلب المعاملات', 500, $e->getMessage());
        }
    }
}

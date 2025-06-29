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

}

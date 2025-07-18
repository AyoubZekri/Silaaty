<?php

namespace App\Http\Controllers\User\Notification;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShwoNotification extends Controller
{
    public function index()
    {
        try {
            $products = Notifications::where('user_id', auth()->id())->get();

            return Respons::success(['Notifications' => $products]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الشعارات', 500, $e->getMessage());
        }
    }


    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $notification = Notifications::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $notification->update(['is_read' => 1]);

            return Respons::success(['Notifications' => $notification]);
        } catch (\Exception $e) {
            return Respons::error('الإشعار غير موجود أو غير مسموح الوصول إليه', 404);
        }
    }

}

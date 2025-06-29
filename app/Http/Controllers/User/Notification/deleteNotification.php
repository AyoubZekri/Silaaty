<?php

namespace App\Http\Controllers\User\Notification;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class deleteNotification extends Controller
{
    public function deletenot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);

        if ($validator->fails()) {
            return Respons::error("خطأ في البيانات", 422, $validator->errors());
        }

        try {
            $Notification = Notifications::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$Notification) {
                return Respons::error('الشعار غير موجود', 404);
            }

            $Notification->delete();

            return Respons::success('تم حذف الشعار بنجاح');
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء حذف الشعار', 500, $e->getMessage());
        }
    }

}

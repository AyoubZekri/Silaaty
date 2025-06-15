<?php

namespace App\Http\Controllers\User\Report;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteReportController extends Controller
{
    public static function Delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|exists:reports,id",
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $Reports = Report::where("id", $request->id)->where('report_id', auth()->id())
                ->first();

            $Reports->delete();

            return Respons::success();

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الابلاغات', 500, $e->getMessage());
        }
    }
}

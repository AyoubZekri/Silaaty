<?php

namespace App\Http\Controllers\User\Report;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShwoReportController extends Controller
{

    public static function index()
    {
        try {
            $Reports = Report::where("report_id",auth()->id())->get();

            return Respons::success([
                "Report" => $Reports,
            ]);
        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الابلاغات', 500, $e->getMessage());
        }
    }


    public static function Show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|exists:reports,id",
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $Reports = Report::where("id", $request->id)->first();

            return Respons::success([
                "Report" => $Reports,
            ]);

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب الابلاغات', 500, $e->getMessage());
        }
    }
}

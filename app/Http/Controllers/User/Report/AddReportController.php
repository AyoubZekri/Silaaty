<?php

namespace App\Http\Controllers\User\Report;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddReportController extends Controller
{
    public static function AddReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "report"=>"required|string|max:255",
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $data = $request->all();
            $data['report_id'] = auth()->id();

            $report = Report::create($data);

            return Respons::success();

            } catch (\Exception $e) {
                return Respons::error('حدث خطأ أثناء  الإبلاغ', 500, $e->getMessage());
            }


    }
}

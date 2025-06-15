<?php

namespace App\Http\Controllers\User\Report;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateReportController extends Controller
{
    public static function Update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id"=>"required|exists:reports,id",
                "report"=>"required|string|max:255",
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $report = Report::where('id', $request->id)
            ->where('report_id', auth()->id())
            ->firstOrFail();

            $data = $request->all();

            $report->update($data);

            return Respons::success();

            } catch (\Exception $e) {
                return Respons::error('حدث خطأ أثناء  الإبلاغ', 500, $e->getMessage());
            }


    }
}

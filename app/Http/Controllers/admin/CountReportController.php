<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CountReportController extends Controller
{
    public function CountReport()
    {
        $clinicReports = Report::select('reported_id', DB::raw('count(*) as report_count'))
            ->with("reported", function ($query) {
                $query->where("user_role", 3)->with('user_Clinic');
            })
            ->groupBy('reported_id')
            ->get()
            ->map(function ($report) {
                $clinic = optional($report->reported);
                return [
                    "id" => $clinic->id,
                    'clinic_name' => $clinic->name,
                    // 'pharm_name_fr' => $clinic->pharm_name_fr,
                    // 'profile_image' => $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null,
                    // 'address' => $clinic->address,
                    'report_count' => $report->report_count,
                ];
            });

        return response()->json([
            "status" => 1,
            "message" => 'success',
            "data" => $clinicReports
        ], 200);
    }

}

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
            ->whereHas("reported", function ($query) {
                $query->where("user_role", 3);
            })
            ->with("reported.clinic")
            ->groupBy('reported_id')
            ->paginate(10)
            ->through(function ($report) {
                $clinic = optional($report->reported->clinic);
                return [
                    "id" => $clinic->id,
                    'clinic_name' => $clinic->name,
                    'pharm_name_fr' => $clinic->pharm_name_fr,
                    'profile_image' => $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null,
                    'address' => $clinic->address,
                    'report_count' => $report->report_count,
                ];
            });

        return response()->json([
            "status" => 1,
            "message" => 'success',
            "data" => [
                'data' => $clinicReports->items(),
                'meta' => [
                    'current_page' => $clinicReports->currentPage(),
                    'last_page' => $clinicReports->lastPage(),
                    'per_page' => $clinicReports->perPage(),
                    'total' => $clinicReports->total(),
                    'count' => $clinicReports->count(),
                ]
            ]
        ], 200);
    }

}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Report;
use App\Models\User;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CountReportController extends Controller
{

    public function CountReport()
    {
        $reportCounts = Report::select('reported_id', DB::raw('count(*) as report_count'))
            ->groupBy('reported_id')
            ->paginate(10);

        $clinicIds = $reportCounts->pluck('reported_id')->toArray();

        $clinics = Clinic::with('municipality')
            ->whereIn('id', $clinicIds)
            ->get()
            ->keyBy('id');

        $data = $reportCounts->map(function ($report) use ($clinics) {
            $clinic = $clinics->get($report->reported_id);

            return [
                'id' => $clinic->id,
                'name' => $clinic->name,
                'address' => $clinic->address,
                'phone' => $clinic->phone,
                'email' => $clinic->email,
                'latitude' => $clinic->latitude,
                'longitude' => $clinic->longitude,
                'type' => $clinic->type,
                'pharm_name_fr' => $clinic->pharm_name_fr,
                'cover_image' => $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null,
                'profile_image' => $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null,
                'municipality' => $clinic->municipality->name ?? null,
                'report_count' => $report->report_count,
            ];
        });

        return response()->json([
            'status' => 1,
            'message' => 'success',
            'data' => [
                'data' => $data->toArray(),
                'meta' => [
                    'current_page' => $reportCounts->currentPage(),
                    'last_page' => $reportCounts->lastPage(),
                    'per_page' => $reportCounts->perPage(),
                    'total' => $reportCounts->total(),
                    'count' => $reportCounts->count(),
                ],
            ],
        ], 200);
    }


}

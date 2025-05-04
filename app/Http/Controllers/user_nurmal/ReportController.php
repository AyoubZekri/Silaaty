<?php

namespace App\Http\Controllers\user_nurmal;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $report = Report::paginate(10);
        $reports = $report->getCollection()->map(function ($report) {
            return [
                "id" => $report->id,
                "reporter_id" => $report->reporter_id,
                "reported_id" => $report->reported_id,
            ];
        });

        $report->setCollection($reports);
        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => [
                "data" => $report->items(),
                'meta' => [
                    'current_page' => $report->currentPage(),
                    'last_page' => $report->lastPage(),
                    'per_page' => $report->perPage(),
                    'total' => $report->total(),
                    'count' => $report->count(),
                ]
            ]
        ]);
    }

    public function show(Request $request)
    {
        $request->validate([
            "id" => "required"
        ]);

        $reports = Report::where("id", $request->id)->with(['reporter', 'reported'])->get();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'reports' => $reports
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id|different:reporter_id',
        ]);

        $existingReport = Report::where('reporter_id', $request->reporter_id)
            ->where('reported_id', $request->reported_id)
            ->first();

        if ($existingReport) {
            return response()->json([
                'status' => 0,
                'message' => 'لقد قمت بالإبلاغ عن هذه العيادة من قبل.',
            ], 409);
        }


        $report = Report::create([
            'reporter_id' => $request->reporter_id,
            'reported_id' => $request->reported_id,
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'report' => $report
        ]);
    }

    public function destroy(Request $request)
    {

        $request->validate([
            "id" => "required"
        ]);
        $report = Report::find($request->id);

        if (!$report) {
            return response()->json([
                'status' => 0,
                'message' => 'البلاغ غير موجود'
            ], 404);
        }

        $report->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }
}

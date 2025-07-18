<?php

namespace App\Http\Controllers\Dashbaord\Report;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class Reportcontroller extends Controller
{
    public function index()
    {
        return view("content.admin.Report.Report");
    }

    public function list(Request $request)
    {
        try {
            $data = Report::with('user')->latest();
            if ($request->ajax()) {


                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
                    })
                    ->addColumn('action', function ($row) {
                        // $btn = '<button class="btn btn-sm btn-primary update" data-id="' . $row->id . '" title="رد على الإبلاغ"><i class="bx bx-reply"></i></button>';
                        $btn = ' <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '"><i class="bx bx-trash"></i></button>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return response()->json([
                "data" => $data,
                'status' => false,
                'message' => 'Invalid request type.'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('User list error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error fetching users.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $report = Report::find($id);

            if (!$report) {
                return response()->json([
                    'status' => false,
                    'message' => 'البلاغ غير موجود.'
                ], 404);
            }

            $report->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف البلاغ بنجاح.'
            ]);
        } catch (\Exception $e) {
            Log::error('حذف البلاغ فشل: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف البلاغ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}

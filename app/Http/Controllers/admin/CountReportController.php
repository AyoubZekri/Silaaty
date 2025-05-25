<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CountReportController extends Controller
{
    public function CountReport()
    {
        // تجميع التقارير على أساس العيادة المبلّغ عنها
        $reportCounts = Report::select('reported_id', DB::raw('count(*) as report_count'))
            ->whereHas("reported.user", function ($query) {
                $query->where("user_role", 3); // تأكد أن المستخدم المرتبط بالعيادة دوره "عيادة"
            })
            ->groupBy('reported_id')
            ->paginate(10);

        // نجيب الـ IDs الخاصة بالعيادات
        $clinicIds = $reportCounts->pluck('reported_id')->toArray();

        // جلب بيانات العيادات مع المستخدمين والبلديات المرتبطين
        $clinics = Clinic::with(['user', 'municipality'])
            ->whereIn('id', $clinicIds)
            ->get()
            ->keyBy('id');

        // تجهيز النتيجة
        $reportCounts->getCollection()->transform(function ($report) use ($clinics) {
            $clinic = $clinics[$report->reported_id] ?? null;

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
            "status" => 1,
            "message" => 'success',
            "data" => [
                'data' => $reportCounts->items(),
                'meta' => [
                    'current_page' => $reportCounts->currentPage(),
                    'last_page' => $reportCounts->lastPage(),
                    'per_page' => $reportCounts->perPage(),
                    'total' => $reportCounts->total(),
                    'count' => $reportCounts->count(),
                ]
            ]
        ], 200);
    }


}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use DB;
use Google\Rpc\Status;
use Illuminate\Http\Request;

class CountReportController extends Controller
{
    public function CountReport()
    {
        $clinicReport = Report::select('reported_id', DB::raw('count(*) as report_count'))
            ->whereHas("reported", function ($query)  {
              $query->where("user_role" , 3);
            })
            ->groupBy('reported_id')
            ->with('reported:id,name,pharm_name_fr,profile_image,address')
            ->get();


        return response()->json([
            "status" => 1,
            "message"=> 'success',
            "data"=>$clinicReport
        ],200);
    }
}

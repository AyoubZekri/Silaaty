<?php

namespace App\Http\Controllers\Dashbaord\dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Auth;

class Analytics extends Controller
{
  public function index()
  {
    //dd(Auth::id());
    return view('content.dashboard.dashboards-analytics');
  }



    public function userStats()
    {
        $totalUsers = User:: where("Status",2)->where("user_role",2)->count();
        $totalIncome = $totalUsers*10000;
        $activatedUsers = User::where('Status', 3)->where("user_role", 2)->count();
        $experimentUsers = User::where('Status', 2)->where("user_role", 2)->count();
        $notActivatedUsers = User::where('Status', 1)->where("user_role", 2)->count();
        $nisab = Zakat::first()?->zakat_nisab;
        return response()->json([
            "nisab"=>$nisab,
            'totalUsers' => $totalUsers,
            'totalIncome' => $totalIncome,
            'activatedUsers' => $activatedUsers,
            'experimentUsers' => $experimentUsers,
            'notActivatedUsers' => $notActivatedUsers,
        ]);
    }

    public function updateNisab(Request $request)
    {
        $request->validate([
            'threshold' => 'required|numeric|min:0',
        ]);

        Zakat::query()->update([
            'zakat_nisab' => $request->threshold
        ]);

        return back()->with('success', 'تم تحديث النصاب لكل السجلات بنجاح.');
    }

}

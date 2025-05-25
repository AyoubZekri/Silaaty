<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Municipality;
use App\Models\specialties;
use App\Models\User;
use Google\Rpc\Status;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        $normalUsersCount = User::where('user_role', '2')->count();
        $doctorsCount = Doctor::all()->count();

        $approvedClinics = Clinic::where('Statue', '1')->count();
        $rejectedClinics = Clinic::where('Statue', '2')->count();
        $pendingClinics = Clinic::where('Statue', '0')->count();

        $municipality = Municipality::all()->count();
        $specialties = specialties::all()->count();

        return response()->json([
            'Status' => 0,
            'message' => 'success',
            'normal' => $normalUsersCount,
            'doctors' => $doctorsCount,
            'approved' => $approvedClinics,
            'rejected' => $rejectedClinics,
            'pending' => $pendingClinics,
            'municipality'=>$municipality,
            'specialties'=>$specialties
        ]);
    }
}

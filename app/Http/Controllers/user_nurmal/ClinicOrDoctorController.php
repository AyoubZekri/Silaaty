<?php

namespace App\Http\Controllers\user_nurmal;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\clinic_schedules;
use Illuminate\Http\Request;

class ClinicOrDoctorController extends Controller
{
    public function ClinicAndDoctor(Request $request)
    {
        $request->validate([
            "id" => "required"
        ]);

        $clinic = Clinic::with('doctors.specialty')->find($request->id);

        if (!$clinic) {
            return response()->json([
                'status' => 0,
                'message' => 'العيادة غير موجودة',
            ]);
        }

        $data = [
            'id' => $clinic->id,
            'name' => $clinic->name,
            'address' => $clinic->address,
            'email' => $clinic->email,
            'phone' => $clinic->phone,
            'latitude' => $clinic->latitude,
            'longitude' => $clinic->longitude,
            'doctors' => $clinic->doctors->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'specialty' => $doctor->specialty->name ?? null,
                ];
            }),
        ];

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $data
        ]);
    }
}

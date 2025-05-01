<?php

namespace App\Http\Controllers\user_nurmal;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\clinic_schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ClinicOrDoctorController extends Controller
{
    public function ClinicAndDoctor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer|exists:clinics,id",
            "search" => "nullable|string|max:255",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $clinic = Clinic::with(['doctors.specialty'])
            ->find($request->input('id'));

        if (!$clinic) {
            return response()->json([
                'status' => 0,
                'message' => 'العيادة غير موجودة',
            ]);
        }

        $search = $request->input('search', '');

        $doctors = $clinic->doctors()->when($search, function ($query, $search) {
            return $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhereHas('specialty', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
        })->get();

        $data = [
            'id' => $clinic->id,
            'name' => $clinic->name,
            'address' => $clinic->address,
            'email' => $clinic->email,
            'phone' => $clinic->phone,
            'latitude' => $clinic->latitude,
            'longitude' => $clinic->longitude,
            'doctors' => $doctors->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'specialty_name' => $doctor->specialty->name ?? null,
                    'name_clinic' => $doctor->clinic->name,
                    'profile_image' => $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : null,
                    'Presence' => $doctor->Presence,
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

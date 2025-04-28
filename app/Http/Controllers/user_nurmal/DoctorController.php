<?php

namespace App\Http\Controllers\user_nurmal;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class DoctorController extends Controller
{
    public function Doctorall(Request $request)
    {
        $validator = validator::make($request->all(), [
            'pagination' => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            if ($request->pagination == true) {
                $doctors = Doctor::with(['specialty', 'clinic'])
                    ->select('id', 'name', 'email', 'phone', 'specialties_id', 'clinic_id')
                    ->paginate(10);

                $data = $doctors->getCollection()->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'email' => $doctor->email,
                        'phone' => $doctor->phone,
                        'specialty_name' => $doctor->specialty ? $doctor->specialty->name : null,
                        'name_clinic' => $doctor->clinic->name,
                        'profile_image' => $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : null,
                        'Presence' => $doctor->Presence,
                    ];
                });

                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'data'=>[
                        'data' => $data,
                        'meta' => [
                            'current_page' => $doctors->currentPage(),
                            'last_page' => $doctors->lastPage(),
                            'per_page' => $doctors->perPage(),
                            'total' => $doctors->total(),
                            'count' => $doctors->count(),
                        ]
                    ],

                ], 200);

            } else {
                $doctors = Doctor::with(['specialty', 'clinic'])
                    ->select('id', 'name', 'email', 'phone', 'specialties_id', 'clinic_id')
                    ->get();

                $data = $doctors->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'email' => $doctor->email,
                        'phone' => $doctor->phone,
                        'specialty_name' => $doctor->specialty ? $doctor->specialty->name : null,
                        'name_clinic' => $doctor->clinic->name,
                        'profile_image' => $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : null,
                        'Presence' => $doctor->Presence,

                    ];
                });

                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'data' => $data,
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}

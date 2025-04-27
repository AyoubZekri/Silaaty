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
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()->first()
            ], 422);
        }
        try {
            if ($request->pagination == true) {
                $doctors = Doctor::with([
                    'specialty',
                    'clinic'
                ])
                    ->select('id', 'name', 'email', 'phone', 'specialty_id', 'clinic_id')
                    ->paginate(10);

                $data = $doctors->getCollection()->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'email' => $doctor->email,
                        'phone' => $doctor->phone,
                        'specialty_name' => $doctor->specialty ? $doctor->specialty->name : null,
                        'clinic' => $doctor->clinic ? [
                            'id' => $doctor->clinic->id,
                            'name' => $doctor->clinic->name,
                            'address' => $doctor->clinic->address,
                            'phone' => $doctor->clinic->phone,
                            'email' => $doctor->clinic->email,
                            'pharm_name_fr' => $doctor->clinic->pharm_name_fr,
                        ] : null,
                    ];
                });

                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'data' => $data,
                    'meta' => [
                        'current_page' => $doctors->currentPage(),
                        'last_page' => $doctors->lastPage(),
                        'per_page' => $doctors->perPage(),
                        'total' => $doctors->total(),
                    ]
                ], 200);
            } else {
                $doctors = Doctor::with([
                    'specialty',
                    'clinic'
                ])
                    ->select('id', 'name', 'email', 'phone', 'specialty_id', 'clinic_id')
                    ->get();

                $data = $doctors->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'email' => $doctor->email,
                        'phone' => $doctor->phone,
                        'specialty_name' => $doctor->specialty ? $doctor->specialty->name : null,
                        'clinic' => $doctor->clinic ? [
                            'id' => $doctor->clinic->id,
                            'name' => $doctor->clinic->name,
                            'address' => $doctor->clinic->address,
                            'phone' => $doctor->clinic->phone,
                            'email' => $doctor->clinic->email,
                            'pharm_name_fr' => $doctor->clinic->pharm_name_fr,
                        ] : null,
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

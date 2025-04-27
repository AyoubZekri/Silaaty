<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{

    // doctor
    public function index($id)
    {
        if (!Clinic::where('id', $id)->exists()) {
            return response()->json([
                'status' => 0,
                'message' => 'العيادة غير موجودة'
            ], 404);
        }

        $doctors = Doctor::with('clinic')
            ->where('clinic_id', $id)
            ->with("schedules")
            ->get();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'doctors' => $doctors
        ], 200);
    }

    public function allDoctor(Request $request)
    {
        $validator = validator::make($request->all(), [
            'pagination' => "required"
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
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


    public function showdoctor($id)
    {
        try {
            $doctor = Doctor::with('schedules')->find($id);

            if (!$doctor) {
                return response()->json([
                    'status' => 0,
                    'message' => 'الطبيب غير موجودة'
                ], 404);
            }

            // $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
            // $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
            // $clinic->specialty_name = $clinic->specialty ? $clinic->specialty->name : null;
            // unset($clinic->specialty);

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'clinic' => $doctor
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6',
            'clinic_id' => 'required|exists:clinics,id',
            'specialties_id' => 'required|exists:specialties,id',
        ]);

        $days = collect([
            ['day' => 'السبت', 'opening_time' => '08:30', 'closing_time' => '12:30'],
            ['day' => 'الأحد', 'opening_time' => '08:30', 'closing_time' => '12:30'],
            ['day' => 'الإثنين', 'opening_time' => '08:30', 'closing_time' => '12:30'],
            ['day' => 'الثلاثاء', 'opening_time' => '08:30', 'closing_time' => '12:30'],
            ['day' => 'الأربعاء', 'opening_time' => '08:30', 'closing_time' => '12:30'],
            ['day' => 'الخميس', 'opening_time' => '08:30', 'closing_time' => '12:30'],
            ['day' => 'الجمعة', 'opening_time' => '08:30', 'closing_time' => '12:30'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified' => rand(10000, 99999),
                'user_role' => 4,
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'clinic_id' => $request->clinic_id,
                'specialties_id' => $request->specialties_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            foreach ($days as $day) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'day' => $day['day'],
                    'opening_time' => $day['opening_time'],
                    'closing_time' => $day['closing_time'],
                ]);
            }


            $DoctorRole = Role::where('role_name', 'Doctor')->first();

            if ($DoctorRole) {
                $user->user_roles()->attach($DoctorRole->id);
            }
            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'doctor' => $doctor,
                'user' => $user
            ], 201);
        } catch (Exception $th) {
            DB::rollBack();
            return response()->json([
                'status' => "false",
                'message' => "حدث خطأ اثناء إنشاء الحساب",
                'error' => $th->getMessage(),
            ]);
        }

    }




    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'status' => 0,
                'message' => 'الطبيب غير موجود'
            ], 404);
        }


        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'phone' => 'required|string|max:15',
            'password' => 'nullable|string|min:6',
            'specialties_id' => 'required|exists:specialties,id',
            'clinic_id' => 'required|exists:clinics,id',

        ]);
        DB::beginTransaction();
        try {
            $doctor->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $doctor->user->password,
            ]);

            $doctor->update([
                'clinic_id' => $request->clinic_id,
                'specialties_id' => $request->specialties_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'doctor' => $doctor
            ], 200);
        } catch (Exception $th) {
            DB::rollBack();
            return response()->json([
                'status' => "false",
                'message' => "حدث خطأ اثناء تعديل الحساب",
                'error' => $th->getMessage(),
            ]);
        }


    }

    public function destroy($id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'status' => 0,
                'message' => 'الطبيب غير موجود'
            ], 404);
        }

        $doctor->user->delete();

        $doctor->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ], 200);
    }
}

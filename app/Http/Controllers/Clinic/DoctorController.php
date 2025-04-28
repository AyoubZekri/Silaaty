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
use Illuminate\Support\Facades\Storage;
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

        $doctors->profile_image = $doctors->profile_image ? asset('storage/' . $doctors->profile_image) : null;


        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'doctors' => $doctors
        ], 200);
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
            $doctor->profile_image = $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : null;


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
            'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
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

            $profile_image = $request->file('profile_image')->store('doctor_images', 'public');

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
                'profile_image'=>$profile_image
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
            'specialties_id' => 'required|exists:specialties,id',
            'clinic_id' => 'required|exists:clinics,id',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        DB::beginTransaction();
        try {

            if ($request->hasFile('profile_image')) {
                if ($doctor->profile_image) {
                    Storage::disk('public')->delete($doctor->profile_image);
                }
                $profileImagePath = $request->file('profile_image')->store('doctor_images', 'public');
            } else {
                $profileImagePath = $doctor->profile_image;
            }
            $doctor->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $doctor->update([
                'clinic_id' => $request->clinic_id,
                'specialties_id' => $request->specialties_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'profile_image' => $profileImagePath,
            ]);
            DB::commit();

            $doctor->profile_image = $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : null;

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

        DB::beginTransaction();
        try {
            $profileImagePath = $doctor->profile_image;

            $doctor->user->delete();

            $doctor->delete();

            if ($profileImagePath) {
                Storage::disk('public')->delete($profileImagePath);
            }

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'تم حذف الطبيب بنجاح',
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء عملية الحذف',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

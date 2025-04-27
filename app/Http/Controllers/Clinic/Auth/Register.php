<?php

namespace App\Http\Controllers\Clinic\Auth;

use App\Http\Controllers\Controller;
use App\Mail\confermemail;
use App\Models\clinic_schedules;
use App\Models\DoctorSchedule;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Symfony\Contracts\Service\Attribute\Required;
use Illuminate\Support\Facades\DB;
class Register extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_fr' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'register' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'municipalitie_id' => 'required|exists:municipalities,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'clinic_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            "phone" => "required|String|min:10|max:12",
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

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $registerPath = $request->file('register')->store('clinic_registers', 'public');

            $clinicImagePath = $request->file('clinic_image')->store('clinic_images', 'public');
            $cover_image = $request->file('cover_image')->store('clinic_images', 'public');

            $email_verified_at = rand(10000, 99999);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified' => $email_verified_at,
                'user_role' => 3,
                "phone" => $request->phone,
            ]);


            $clinic = Clinic::create([
                'user_id' => $user->id,
                'municipalities_id' => $request->municipalitie_id,
                'name' => $request->name,
                'pharm_name_fr' => $request->name_fr,
                'address' => $request->address,
                'register' => $registerPath,
                'email' => $user->email,
                'profile_image' => $clinicImagePath,
                "cover_image" => $cover_image,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                "phone" => $request->phone,
                'type' => 'عيادة'
            ]);


            foreach ($days as $day) {
                $schedules = clinic_schedules::create([
                    'clinic_id' => $clinic->id,
                    'day' => $day['day'],
                    'opening_time' => $day['opening_time'],
                    'closing_time' => $day['closing_time'],
                ]);
            }

            $ClinicRole = Role::where('role_name', 'Clinic')->first();

            if ($ClinicRole) {
                $user->user_roles()->attach($ClinicRole->id);
            }


            Mail::to($user->email)->send(new confermemail($user));

            $token = $user->createToken('API Token')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'user' => $user,
                'clinic' => $clinic,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء إنشاء الحساب أو العيادة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $clinic = Clinic::find($id);
        if (!$clinic) {
            return response()->json([
                'status' => 0,
                'message' => 'العيادة غير موجودة'
            ], 404);
        }

        $clinic->update($request->only(['name', 'pharm_name_fr', 'address', 'latitude', 'longitude']));

        if ($request->hasFile('clinic_image')) {
            Storage::delete('public/' . $clinic->profile_image);
            $clinic->profile_image = $request->file('clinic_image')->store('clinic_images', 'public');
        }

        if ($request->hasFile('cover_image')) {
            Storage::delete('public/' . $clinic->cover_image);
            $clinic->cover_image = $request->file('cover_image')->store('clinic_images', 'public');
        }

        $clinic->save();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'clinic' => $clinic
        ]);
    }

    public function destroy($id)
    {
        $clinic = Clinic::find($id);
        if (!$clinic) {
            return response()->json([
                'status' => 0,
                'message' => 'العيادة غير موجودة'
            ], 404);
        }

        Storage::delete(['public/' . $clinic->profile_image, 'public/' . $clinic->cover_image, 'public/' . $clinic->register]);
        $clinic->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\clinic_schedules;
use App\Models\Doctor;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{


    public function getSchedulesByClinicId(Request $request)
    {

        $data = $request->validate([
            'clinic_id' => "required|exists:clinics,id",
        ]);

        try {
            $schedule = clinic_schedules::where('clinic_id', $data['clinic_id'])->get();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'data' => $schedule,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function addSchedules(Request $request)
    {
        try {
            $data = $request->validate([
                'clinic_id' => "required|exists:clinics,id",
                'day' => "required|string",
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i|after:opening_time'
            ]);

            $existingSchedule = clinic_schedules::where('clinic_id', $data['clinic_id'])
                ->where('day', $data['day'])
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'status' => 0,
                    'message' => 'اليوم موجود بالفعل.',
                ], 400);
            }

            $Schedules = clinic_schedules::create($data);
            return response()->json([
                'status' => 1,
                'message' => 'Success',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function UpdataSchedules(Request $request)
    {
        $data = $request->validate([
            "id"=>"required",
            'clinic_id' => "required|exists:clinics,id",
            'day' => "required|string",
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time'
        ]);

        try {
            $schedule = clinic_schedules::findOrFail($data["id"]);

            $schedule = clinic_schedules::find($data["id"]);

            if (!$schedule) {
                return response()->json([
                    'status' => 0,
                    'message' => 'الجدول غير موجود',
                ], 404);
            }


            $schedule->update($data);
            return response()->json([
                'status' => 1,
                'message' => 'Success',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            "id" => "required",
        ]);
        $schedule = clinic_schedules::findOrFail($data["id"]);

        $schedule->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }
}



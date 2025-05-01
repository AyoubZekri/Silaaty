<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    public function getSchedulesByClinicId(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => "required|exists:doctors,id",
        ]);
        try {
            $schedule = DoctorSchedule::where('doctor_id', $data['doctor_id'])->get();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'data' => $schedule,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function addSchedules(Request $request)
    {
        try {
            $data = $request->validate([
                'doctor_id' => "required|exists:doctors,id",
                'day' => "required|string",
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i|after:opening_time'
            ]);


            $existingSchedule = DoctorSchedule::where('doctor_id', $data['doctor_id'])
                ->where('day', $data['day'])
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'status' => 0,
                    'message' => 'اليوم موجود بالفعل.',
                ], 400);
            }

            $Schedules = DoctorSchedule::create($data);
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

    public function UpdataSchedules(Request $request, $id)
    {

        $data = $request->validate([
            'id'=> "required",
            'doctor_id' => "required|exists:doctors,id",
            'day' => "required|string",
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time'
        ]);
        try {
            $schedule = DoctorSchedule::findOrFail($data['id']);

            $schedule = DoctorSchedule::find($data['id']);

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
                'message' => 'حدث خطأ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'id'=> 'required'
        ]);
        $schedule = DoctorSchedule::findOrFail($data['id']);

        $schedule->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }

}

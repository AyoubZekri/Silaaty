<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    public function getSchedulesByClinicId($doctor_id)
    {
        try {
            $schedule = DoctorSchedule::where('doctor_id', $doctor_id)->get();

            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ',
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
        try {
            $schedule = DoctorSchedule::findOrFail($id);

            $schedule = DoctorSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'status' => 0,
                    'message' => 'الجدول غير موجود',
                ], 404);
            }

            $data = $request->validate([
                'doctor_id' => "required|exists:doctors,id",
                'day' => "required|string",
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i|after:opening_time'
            ]);

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

    public function delete($id)
    {
        $schedule = DoctorSchedule::findOrFail($id);

        $schedule->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }

}

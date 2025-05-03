<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clinic;
use Exception;
use Illuminate\Support\Facades\Validator;

class ClinicConfermController extends Controller
{
    public function allClinicsNotConferm()
    {
        try {
            $clinics = Clinic::with('schedules', 'municipality')->where('Statue', 0)->get()->map(function ($clinic) {
                $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
                $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
                return $clinic;
            });

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'count' => $clinics->count(),
                'clinics' => $clinics->toArray()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function infoClinicsNotConferm(Request $request)
    {
       $validator = Validator::make($request->all(),[
             'id_clinic' => "required|exists:clinics,id",
       ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        try {


            $clinics = Clinic::with('schedules', 'municipality',)
                ->where("id", $request->id_clinic)
                ->where('Statue', 0)
                ->first();

            if (!$clinics) {
                return response()->json([
                    'status' => 0,
                    'message' => 'العيادة غير موجودة أو تم تأكيدها بالفعل',
                ], 404);
            }
            // 
            $clinics->cover_image = $clinics->cover_image ? asset('storage/' . $clinics->cover_image) : null;
            $clinics->profile_image = $clinics->profile_image ? asset('storage/' . $clinics->profile_image) : null;
            $clinics->register = $clinics->register ? asset('storage/' . $clinics->register) : null;

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'count' => $clinics->count(),
                'clinics' => $clinics->toArray()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approveClinic(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:clinics,id',
        ]);

        try {
            $clinic = Clinic::findOrFail($validated['id']);

            if ($clinic->Statue == 1) {
                return response()->json(['message' => 'العيادة معتمدة بالفعل'], 200);
            }

            $clinic->Statue = 1;
            $clinic->save();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء معالجة الطلب',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function RefusalClinic(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:clinics,id',
        ]);

        try {
            $clinic = Clinic::findOrFail($validated['id']);



            $clinic->Statue = 2;
            $clinic->save();

            return response()->json([
                'status' => 1,
                'message' => 'تم رفض العيادة بنجاح',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء معالجة الطلب',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function ReportClinic(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        try {
            $clinic = Clinic::find($validated['id']);

            if (!$clinic) {
                return response()->json(
                    [
                        "status" => 0,
                        'message' => "العيادة غير موجودة"
                    ]
                    ,
                    404
                );
            }

            if ($clinic->Statue == 3) {
                return response()->json(
                    [
                        "status" => 0,
                        'message' => 'تم حضر العيادة بالفعل'
                    ]
                    ,
                    403
                );
            }


            $clinic->Statue = 3;
            $clinic->save();

            return response()->json([
                'status' => 1,
                'message' => 'تم حضر العيادة  العيادة بنجاح',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء معالجة الطلب',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchClinicsNotConferm(Request $request)
    {
        try {
            $search = $request->input('query');

            $query = Clinic::with(['schedules', 'municipality'])
                ->where('Statue', 0)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('address', 'LIKE', "%$search%")
                        ->orWhere('pharm_name_fr', 'LIKE', "%$search%")
                        ->orWhereHas('municipality', function ($mq) use ($search) {
                            $mq->where('name', 'LIKE', "%$search%");
                        });
                });

            $clinics = $query->get();

            $clinics = $clinics->map(function ($clinic) {
                return [
                    'id' => $clinic->id,
                    'name' => $clinic->name,
                    'address' => $clinic->address,
                    'phone' => $clinic->phone,
                    'email' => $clinic->email,
                    'pharm_name_fr' => $clinic->pharm_name_fr,
                    'type' => $clinic->type,
                    'latitude' => $clinic->latitude,
                    'longitude' => $clinic->longitude,
                    'cover_image' => $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null,
                    'profile_image' => $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null,
                    'municipality' => $clinic->municipality->name ?? null,
                    'schedules' => $clinic->schedules->map(function ($schedule) {
                        return [
                            'day' => $schedule->day,
                            'start_time' => $schedule->start_time,
                            'end_time' => $schedule->end_time,
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'count' => $clinics->count(),
                'clinics' => $clinics
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء البحث',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}

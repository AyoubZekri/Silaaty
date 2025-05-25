<?php

namespace App\Http\Controllers\user_nurmal;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClinicController extends Controller
{




    public function ClinicMap()
    {
        try {
            $clinics = Clinic::select(
                'id',
                'name',
                'latitude',
                'longitude',
                'Statue'
            )->where('Statue', 1)
                ->get();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'clinics' => $clinics
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchClinicMap(Request $request)
    {
        try {
            $clinicName = $request->input('clinic_name');
            $specialtyId = $request->input('specialty_id');

            $clinics = Clinic::select('id', 'name', 'latitude', 'longitude', 'Statue')
                ->where('Statue', 1)
                ->when($clinicName, function ($query) use ($clinicName) {
                    $query->where('name', 'LIKE', '%' . $clinicName . '%');
                })
                ->when($specialtyId, function ($query) use ($specialtyId) {
                    $query->whereHas('doctors', function ($q) use ($specialtyId) {
                        $q->where('specialties_id', $specialtyId);
                    });
                })
                ->with([
                    'doctors' => function ($query) use ($specialtyId) {
                        $query->when($specialtyId, function ($q) use ($specialtyId) {
                            $q->where('specialties_id', $specialtyId);
                        })->with('schedules');
                    }
                ])
                ->get();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'clinics' => $clinics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء البحث',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function nearbyClinics(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = 2;

            $clinics = Clinic::selectRaw("
            clinics.*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?)) * sin(radians(latitude))
            )) AS distance
        ", [$latitude, $longitude, $latitude])
                ->where('Statue', 1)
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'asc')
                ->with(['municipality', 'schedules'])
                ->paginate(10);

            $data = $clinics->map(function ($clinic) {
                return [
                    'id' => $clinic->id,
                    'name' => $clinic->name,
                    'address' => $clinic->address,
                    'phone' => $clinic->phone,
                    'email' => $clinic->email,
                    'latitude' => $clinic->latitude,
                    'longitude' => $clinic->longitude,
                    'type' => $clinic->type,
                    'pharm_name_fr' => $clinic->pharm_name_fr,
                    'cover_image' => $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null,
                    'profile_image' => $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null,
                    'municipality' => $clinic->municipality->name ?? null,
                    'distance' => round($clinic->distance, 2),
                    'schedules' => $clinic->schedules->map(function ($schedule) {
                        return [
                            'day' => $schedule->day,
                            'start_time' => $schedule->opening_time,
                            'end_time' => $schedule->closing_time,
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'data' => [
                    'data' => $data,
                    'meta' => [
                        'current_page' => $clinics->currentPage(),
                        'last_page' => $clinics->lastPage(),
                        'per_page' => $clinics->perPage(),
                        'total' => $clinics->total(),
                        'count' => $clinics->count(),
                    ]
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function allClinics(Request $request)
    {
        try {
            $clinics = Clinic::with(['schedules', 'municipality'])
                ->where('Statue', 1)
                ->paginate(10); // يمكنك استخدام ->get() إن أردت بدون pagination

            $data = $clinics->map(function ($clinic) {
                return [
                    'id' => $clinic->id,
                    'name' => $clinic->name,
                    'address' => $clinic->address,
                    'phone' => $clinic->phone,
                    'email' => $clinic->email,
                    'latitude' => $clinic->latitude,
                    'longitude' => $clinic->longitude,
                    'type' => $clinic->type,
                    'pharm_name_fr' => $clinic->pharm_name_fr,
                    'cover_image' => $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null,
                    'profile_image' => $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null,
                    'municipality' => $clinic->municipality->name ?? null,
                    'schedules' => $clinic->schedules->map(function ($schedule) {
                        return [
                            'day' => $schedule->day,
                            'start_time' => $schedule->opening_time,
                            'end_time' => $schedule->closing_time,
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'data' => [
                    'data' => $data,
                    'meta' => [
                        'current_page' => $clinics->currentPage(),
                        'last_page' => $clinics->lastPage(),
                        'per_page' => $clinics->perPage(),
                        'total' => $clinics->total(),
                        'count' => $clinics->count(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function showClinic($id)
    {
        try {
            $clinic = Clinic::with('schedules', 'municipality')->where('Statue', 1)->find($id);

            if (!$clinic) {
                return response()->json([
                    'status' => 0,
                    'message' => 'العيادة غير موجودة'
                ], 404);
            }

            $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
            $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
            $clinic->specialty_name = $clinic->specialty ? $clinic->specialty->name : null;
            unset($clinic->specialty);

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'clinic' => $clinic
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function getBySpecialty($id)
    {
        try {
            $clinics = Clinic::whereHas('doctors', function ($query) use ($id) {
                $query->where('specialties_id', $id)->with('schedules');
            })
                ->with([
                    'doctors' => function ($query) use ($id) {
                        $query->where('specialties_id', $id);
                    }
                ])
                ->paginate(10);

            $data = $clinics->map(function ($clinic) {
                return [
                    'id' => $clinic->id,
                    'name' => $clinic->name,
                    'address' => $clinic->address,
                    'phone' => $clinic->phone,
                    'email' => $clinic->email,
                    'latitude' => $clinic->latitude,
                    'longitude' => $clinic->longitude,
                    'type' => $clinic->type,
                    'pharm_name_fr' => $clinic->pharm_name_fr,
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
                    'doctors' => $clinic->doctors->map(function ($doctor) {
                        return [
                            'id' => $doctor->id,
                            'specialty_name' => $doctor->specialty->name ?? null,
                            'name' => $doctor->name,
                            'email' => $doctor->email,
                            'phone' => $doctor->phone,
                            'schedules' => $doctor->schedules->map(function ($schedule) {
                                return [
                                    'day' => $schedule->day,
                                    'start_time' => $schedule->opening_time,
                                    'end_time' => $schedule->closing_time,
                                ];
                            })

                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'data' => [
                    'data' => $data,
                    'meta' => [
                        'current_page' => $clinics->currentPage(),
                        'last_page' => $clinics->lastPage(),
                        'per_page' => $clinics->perPage(),
                        'total' => $clinics->total(),
                        'count' => $clinics->count(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function searchClinics(Request $request)
    {
        try {
            $search = $request->input('query');

            $query = Clinic::with(['schedules', 'municipality'])
                ->where('Statue', 1)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('address', 'LIKE', "%$search%")
                        ->orWhere('pharm_name_fr', 'LIKE', "%$search%")
                        ->orWhereHas('municipality', function ($mq) use ($search) {
                            $mq->where('name', 'LIKE', "%$search%");
                        });
                });

            $clinics = $query->paginate(10);

            $data = $clinics->map(function ($clinic) {
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
                            'start_time' => $schedule->opening_time,
                            'end_time' => $schedule->closing_time,
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'data' => [
                    'data' => $data,
                    'meta' => [
                        'current_page' => $clinics->currentPage(),
                        'last_page' => $clinics->lastPage(),
                        'per_page' => $clinics->perPage(),
                        'total' => $clinics->total(),
                        'count' => $clinics->count(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'حدث خطأ أثناء البحث',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

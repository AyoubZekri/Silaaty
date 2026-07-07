<?php

namespace App\Http\Controllers\Dashbaord\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class Switchcontroller extends Controller
{
    // 2 فترة تجريبية مع تاريخ النهاية
    public function Experiment(Request $request, $id)
    {
        try {
            $request->validate([
                'expires_at' => 'required|date|after_or_equal:today'
            ]);

            $user = User::findOrFail($id);

            if ($user->Status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تاكيد الحساب اولا',
                ]);
            }

            if ($user->Status == 2) {
                return response()->json([
                    'status' => false,
                    'message' => 'تم تفعيل فترة تجريبية بالفعل',
                ]);
            }

            $user->date_experiment = $request->expires_at;
            $user->Status = 2;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تفعيل فترة تجريبية بنجاح',
            ]);

        } catch (\Exception $e) {
            \Log::error('Experiment error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'فشل في التفعيل.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 3 فترت تفعيل مع التاريخ لاكن في التطبيق مشرف فقط
    public function makeExperiment(Request $request, $id)
    {
        try {
            $request->validate([
                'expires_at' => 'required|date|after_or_equal:today'
            ]);

            $user = User::findOrFail($id);

            if ($user->Status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تاكيد الحساب اولا',
                ]);
            }

            if ($user->Status == 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'تم تفعيل فترة الاشتراك بالفعل',
                ]);
            }

            $user->date_experiment = $request->expires_at;
            $user->Status = 3;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تفعيل فترة الاشتراك بنجاح',
            ]);

        } catch (\Exception $e) {
            \Log::error('makeExperiment error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'فشل في التفعيل.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 4 فترة تفعيلية مع تاريخ النهاية في التطبيق مشرف والبائع
    public function Activation(Request $request, $id)
    {
        try {
            $request->validate([
                'expires_at' => 'required|date|after_or_equal:today'
            ]);

            $user = User::findOrFail($id);

            if ($user->Status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تاكيد الحساب اولا',
                ]);
            }

            if ($user->Status == 4) {
                return response()->json([
                    'status' => false,
                    'message' => 'تم تفعيل الحساب بالفعل',
                ]);
            }

            $user->date_experiment = $request->expires_at;
            $user->Status = 4;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تفعيل الحساب بنجاح .',
            ]);

        } catch (\Exception $e) {
            \Log::error('Activation error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'فشل في تحديث الحالة.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 5 فترة دائمة لي تطبيق مشرف فقط
    public function permanentSupervisor($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->Status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تاكيد الحساب اولا',
                ]);
            }

            if ($user->Status == 5) {
                return response()->json([
                    'status' => false,
                    'message' => 'الحساب مفعل دائم للمشرف بالفعل',
                ]);
            }

            $user->date_experiment = null; // لا يوجد تاريخ نهاية
            $user->Status = 5;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم التفعيل الدائم للمشرف بنجاح',
            ]);

        } catch (\Exception $e) {
            \Log::error('permanentSupervisor error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'فشل في التفعيل.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 6 فترة دائمة لي تطبيق البائع والمشرف
    public function permanentSellerSupervisor($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->Status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تاكيد الحساب اولا',
                ]);
            }

            if ($user->Status == 6) {
                return response()->json([
                    'status' => false,
                    'message' => 'الحساب مفعل دائم للمشرف والبائع بالفعل',
                ]);
            }

            $user->date_experiment = null; // لا يوجد تاريخ نهاية
            $user->Status = 6;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم التفعيل الدائم للمشرف والبائع بنجاح',
            ]);

        } catch (\Exception $e) {
            \Log::error('permanentSellerSupervisor error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'فشل في التفعيل.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

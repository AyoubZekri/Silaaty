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
    // 7 تطبيق سطح المكتب مشرف فقط فترة اشتراك فقط
    public function desktopAdminExperiment(Request $request, $id)
    {
        try {
            $request->validate(['expires_at' => 'required|date|after_or_equal:today']);
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 7) return response()->json(['status' => false, 'message' => 'تم تفعيل فترة الاشتراك بالفعل']);
            
            $user->date_experiment = $request->expires_at;
            $user->Status = 7;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم تفعيل فترة الاشتراك بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    // 8 تطبيق سطح المكتب مشرف و بائع فترة اشتراك فقط
    public function desktopSellerAdminExperiment(Request $request, $id)
    {
        try {
            $request->validate(['expires_at' => 'required|date|after_or_equal:today']);
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 8) return response()->json(['status' => false, 'message' => 'تم تفعيل فترة الاشتراك بالفعل']);
            
            $user->date_experiment = $request->expires_at;
            $user->Status = 8;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم تفعيل فترة الاشتراك بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    // 9 تفعيل دائم تطبيق سطح المكتب مشرف
    public function desktopAdminPermanent($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 9) return response()->json(['status' => false, 'message' => 'الحساب مفعل دائم للمشرف بالفعل']);
            
            $user->date_experiment = null;
            $user->Status = 9;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم التفعيل الدائم للمشرف بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    // 10 تفعيل دائم تطبيق بائع ومشرف
    public function desktopSellerAdminPermanent($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 10) return response()->json(['status' => false, 'message' => 'الحساب مفعل دائم للبائع والمشرف بالفعل']);
            
            $user->date_experiment = null;
            $user->Status = 10;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم التفعيل الدائم بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    // 11 مشرف في الهاتف والكمبيوتر تفعيل فترة
    public function mobilePcAdminExperiment(Request $request, $id)
    {
        try {
            $request->validate(['expires_at' => 'required|date|after_or_equal:today']);
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 11) return response()->json(['status' => false, 'message' => 'تم تفعيل فترة الاشتراك بالفعل']);
            
            $user->date_experiment = $request->expires_at;
            $user->Status = 11;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم تفعيل فترة الاشتراك بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    // 12 مشرف وبائع من الهاتف والكمبيوتر تفعيل فترة
    public function mobilePcSellerAdminExperiment(Request $request, $id)
    {
        try {
            $request->validate(['expires_at' => 'required|date|after_or_equal:today']);
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 12) return response()->json(['status' => false, 'message' => 'تم تفعيل فترة الاشتراك بالفعل']);
            
            $user->date_experiment = $request->expires_at;
            $user->Status = 12;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم تفعيل فترة الاشتراك بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    // 13 مشرف من الهاتف والكمبيتر تفعيل دائم
    public function mobilePcAdminPermanent($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 13) return response()->json(['status' => false, 'message' => 'الحساب مفعل دائم للمشرف بالفعل']);
            
            $user->date_experiment = null;
            $user->Status = 13;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم التفعيل الدائم للمشرف بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    // 14 مشرف وبائع من الهاتف والكمبيتر تفعيل دائم
    public function mobilePcSellerAdminPermanent($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->Status == 0) return response()->json(['status' => false, 'message' => 'يجب تاكيد الحساب اولا']);
            if ($user->Status == 14) return response()->json(['status' => false, 'message' => 'الحساب مفعل دائم للبائع والمشرف بالفعل']);
            
            $user->date_experiment = null;
            $user->Status = 14;
            $user->save();
            return response()->json(['status' => true, 'message' => 'تم التفعيل الدائم بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'فشل في التفعيل.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateSellSettings(Request $request, $id)
    {
        try {
            $request->validate([
                'sell_type' => 'required|in:1,2,3',
                'max_sellers' => 'required|integer|min:0'
            ]);

            $user = User::findOrFail($id);
            $user->sell_type = $request->sell_type;
            $user->max_sellers = $request->max_sellers;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث إعدادات البيع بنجاح',
            ]);
        } catch (\Exception $e) {
            \Log::error('updateSellSettings error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'فشل في تحديث إعدادات البيع.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

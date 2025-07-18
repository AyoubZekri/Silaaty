<?php

namespace App\Http\Controllers\Dashbaord\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class Switchcontroller extends Controller
{
    public function Activation($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->Status == 1 || $user->Status == 2) {
                $user->Status = 3;
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'تم تفعيل الحساب .',
                ]);
            }

            if ($user->Status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تاكيد الحساب اولا',
                ]);
            }

            if ($user->Status == 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'تم تفعيل الحساب بالفعل',
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Activation error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'فشل في تحديث الحالة.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function makeExperiment(Request $request, $id)
    {
        $request->validate([
            'expires_at' => 'required|date|after_or_equal:today'
        ]);

        $user = User::findOrFail($id);
        $user->date_experiment = $request->expires_at;
        if ($user->Status == 1) {
            $user->Status = 2;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تفعيل الحساب .',
            ]);
        }

        if ($user->Status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'يجب تاكيد الحساب اولا',
            ]);
        }

        if ($user->Status == 2) {
            return response()->json([
                'status' => false,
                'message' => 'تم تحويل لتجريبي بالفعل',
            ]);
        }


        $user->save();

        return response()->json(['status' => true, 'message' => 'تم التفعيل']);
    }


}

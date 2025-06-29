<?php

namespace App\Http\Controllers\User\Auth;

use App\Function\Respons;
use App\Function\UserService;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateUserController extends Controller
{

    public function UpdateUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'family_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:12',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $user = User::find(auth()->id());
            if (!$user) {
                return Respons::error('المستخدم غير موجود', 404);
            }

            $user->update([
                'name' => $request->name,
                'family_name' => $request->family_name,
                'phone_number' => $request->phone_number,
            ]);

            return Respons::success($user);

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تحديث البيانات', 500, $e->getMessage());
        }
    }


}


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
                "adresse"=>"nullable|string|max:255",
                "logo_stor"=>"nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
                'name' => 'required|string|max:255',
                'family_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:12',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            $data=[
                'name' => $request->name,
                'family_name' => $request->family_name,
                'phone_number' => $request->phone_number,
                'adresse' => $request->adresse,
            ];

            $user = User::find(auth()->id());
            if (!$user) {
                return Respons::error('المستخدم غير موجود', 404);
            }

            if ($request->hasFile('logo_stor')) {
                $path = $request->file('logo_stor')->store('User', 'public');
                $data['logo_stor'] = $path;
            }

            $user->update($data);

            return Respons::success($user);

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء تحديث البيانات', 500, $e->getMessage());
        }
    }


}


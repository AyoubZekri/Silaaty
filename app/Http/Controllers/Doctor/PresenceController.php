<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Google\Rpc\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PresenceController extends Controller
{
    public function Presence(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            "id_doctor"=>'required'
        ]);

        if ($Validator->fails()) {
            return response()->json([
                "status"=>0,
                "message"=>$Validator->errors()->first(),
            ],422);
        }

        $doctor = Doctor::find($request->id_doctor);

        if (!$doctor) {
            return response()->json([
                'status'=>0,
                'message'=>"الطبيب غير موجود"
            ],404);
        }

        $doctor->Presence = $doctor->Presence == 1 ? 0 : 1;
        $doctor->save();

        return response()->json([
            'status'=>1,
            "message"=>'success'
        ],201);


    }
}

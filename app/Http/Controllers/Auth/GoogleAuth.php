<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Laravel\Sanctum\PersonalAccessToken;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use stdClass;


class GoogleAuth extends Controller
{
    public function GoogleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
            'fcm_token' => 'sometimes|string',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        DB::beginTransaction();

        $auth = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->createAuth();



        try {
            $firebase = $auth->getUser($request->uid);
        } catch (UserNotFound $e) {
            return response()->json([
                'message' => 'User not found in Firebase'
            ], 404);
        }




        $roleId = 2;
        $user = User::where('email', $firebase->email)->first();

        if ($user) {

            if ($user->isUser()) {
                $token = $user->createToken('auth_token')->plainTextToken;
                DB::commit();
                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'access_token' => $token,
                    'role_id' => $roleId,
                    'user' => $user,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => ' ليس لديك صلاحيات '
                ], 403);
            }
        } else {
            $user = User::create([
                'name' => $firebase->displayName ?? "no name",
                'email' => $firebase->email,
                'password' => Hash::make("password@1234"),
                //  'google_id' => $firebase->id,
                'user_role' => $roleId
            ]);


            DB::table('roles_user')->insert([
                'user_id' => $user->id,
                'roles_id' => $roleId,
            ]);
            Mail::to($user['email'])->send(new WelcomeMail($user));

            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'access_token' => $token,
                'role_id' => $roleId,
                'user' => $user,
            ]);
        }


    }



    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
            'email' => 'required|email',
            'fcm_token' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ], 400);
        }

        $user = $request->user();

        $data = new stdClass();
        $data->user = $user;

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $data
        ], 200);
    }


    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->delete();
        // $user->tokens()->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }

}

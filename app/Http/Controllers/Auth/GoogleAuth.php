<?php

namespace App\Http\Controllers\auth;

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
            'email' => 'required|email',
            'fcm_token' => 'sometimes|string',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ], 400);
        }



        $auth = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->createAuth();



        try {
            $firebase = $auth->getUser($request->uid);
        } catch (UserNotFound $e) {
            return response()->json([
                'message' => 'User not found in Firebase'
            ], 404);
        }


        if ($firebase->email !== $request->email) {
            return response()->json([
                'message' => 'Email mismatch'
            ], 400);
        }

        $roleId = 2;
        $user = User::where('email', $firebase->email)->first();





        if ($user) {

            if ($user->isUser()) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'role_id' => $roleId,
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'error' => ' ليس لديك صلاحيات '
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
                'role_id' => $roleId,
            ]);
            Mail::to($user['email'])->send(new WelcomeMail($user));

            $token = PersonalAccessToken::updateOrCreateForUser($user, ['name' => 'API Token'])->plainTextToken;

            return response()->json([
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
            'message' => 'Logged out successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->delete();
        // $user->tokens()->delete();

        return response()->json([
            'message' => 'Destroyed successfully'
        ]);
    }

}

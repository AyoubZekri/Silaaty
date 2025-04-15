<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class BearerTokenMiddleware {
    protected $withoutBearerToken = [
        // 'login',
        // Add more route names as needed
    ];

    public function handle(Request $request, Closure $next)
    {
        // Check if the current route is in the list of routes that should bypass Bearer Token check
        if (in_array($request->route()->getName(), $this->withoutBearerToken)) {
            return $next($request);
        }

        // Check if the request has the Bearer token
        if (!$request->bearerToken()) {
            return response()->json([
                'message' => 'Bearer token not provided'
            ], 401);
        }

        // Check if the Bearer token is empty
        if (empty($request->bearerToken())) {
            return response()->json([
                'message' => 'Bearer token is empty'
            ], 401);
        }

        // Check if the Bearer token exists in the access tokens table
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        if (!$accessToken) {
            return response()->json([
                'message' => 'Invalid bearer token'
            ], 401);
        }

        // Check if the user associated with the access token exists
        $user = User::where('id', $accessToken->user_id)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }

        // Authenticate the user
        Auth::login($user);

        return $next($request);
    }
}

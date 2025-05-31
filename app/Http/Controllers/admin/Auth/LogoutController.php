<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Function\Logout_user;
class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        Logout_user::Logout_user($request);
    }
}

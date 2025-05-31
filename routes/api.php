<?php

use App\Http\Controllers\admin\Auth\LoginController;
use App\Http\Controllers\admin\Auth\LogoutController;
use App\Http\Controllers\admin\Auth\RegisterController;
use Illuminate\Support\Facades\Route;




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [LogoutController::class, 'logout']);

    // clinic


    // admin






    // user_normal

    // doctor
});

Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/create', [RegisterController::class, 'Registeradmin']);

Route::post('/User/create', [\App\Http\Controllers\User\Auth\RegisterController::class, "RegisterUser"]);






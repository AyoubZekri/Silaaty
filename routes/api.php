<?php

use App\Http\Controllers\admin\Auth\LoginController;
use App\Http\Controllers\admin\Auth\LogoutController;
use App\Http\Controllers\admin\Auth\RegisterController;
use App\Http\Controllers\admin\ClinicConfermController;
use App\Http\Controllers\admin\MunicipalityController;
use App\Http\Controllers\Clinic\Auth\Login;
use App\Http\Controllers\Clinic\Auth\Register;
use App\Http\Controllers\Clinic\SchedulesController;
use App\Http\Controllers\Doctor\Auth\LogouteController;
use App\Http\Controllers\Doctor\PresenceController;
use App\Http\Controllers\user_nurmal\ClinicOrDoctorController;
use App\Http\Controllers\user_nurmal\ReportController;
use App\Http\Controllers\showSpecialtyController;
use App\Http\Controllers\admin\SpecialtyController;
use App\Http\Controllers\user_nurmal\ClinicController;
use App\Http\Middleware\BearerTokenMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuth;
use App\Http\Controllers\Clinic\DoctorController;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return response()->json($request->user());
// });


// Route::middleware('auth:sanctum')->group(function () {
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [LogoutController::class, 'logout']);
    Route::post('/doctor/logout', [LogouteController::class, 'logout']);
    Route::post('Clinics/logout', [Login::class, 'logout']);
    Route::post('User/logout', [GoogleAuth::class, 'logout']);

    // clinic
    Route::post('Clinics/update', [register::class, 'update']);
    Route::post('Clinics/delete/{id}', [register::class, 'destroy']);

    Route::get('show/doctor/{id}', [DoctorController::class, 'index']);
    Route::post('add/doctor', [DoctorController::class, 'store']);
    Route::post('updata/doctor/{id}', [DoctorController::class, 'update']);
    Route::delete('delete/doctor/{id}', [DoctorController::class, 'destroy']);
    Route::get('doctor/{id}', [DoctorController::class, 'showdoctor']);


    // admin
    Route::post('/clinics/approve', [ClinicConfermController::class, 'approveClinic']);
    Route::post('/clinics/Refusal', [ClinicConfermController::class, 'RefusalClinic']);


    Route::post('Specialty/add', [SpecialtyController::class, 'store']);
    Route::post('Specialty/update/{id}', [SpecialtyController::class, 'update']);
    Route::delete('Specialty/delete/{id}', [SpecialtyController::class, 'destroy']);

    Route::post('Schedules/add', [SchedulesController::class, 'addSchedules']);
    Route::post('Schedules/update/{id}', [SchedulesController::class, 'UpdataSchedules']);
    Route::post('Schedules/delete/{id}', [SchedulesController::class, 'delete']);
    Route::get('Schedules/show/{clinic_id}', [SchedulesController::class, 'getSchedulesByClinicId']);


    Route::post('Municipality/update/{id}', [MunicipalityController::class, 'update']);
    Route::delete('Municipality/delete/{id}', [MunicipalityController::class, 'destroy']);
    Route::post('Municipality/add', [MunicipalityController::class, 'store']);


    // user_normal
    Route::get('/clinics/by-specialty/{id}', [ClinicController::class, 'getBySpecialty']);
    Route::get('/clinics/search/Map', [ClinicController::class, 'searchClinicMap']);

    Route::put('User/update', [GoogleAuth::class, 'update']);

    Route::post("Clinic_and_doctor",[ClinicOrDoctorController::class,"ClinicAndDoctor" ]);
    Route::post('all/doctor', [\App\Http\Controllers\user_nurmal\DoctorController::class, 'Doctorall']);
    Route::post('delet_user/google', [GoogleAuth::class, 'destroy']);
    Route::post('/user/logout', [GoogleAuth::class, 'logout']);

    Route::get('/reports/show', [ReportController::class, 'show']);
    Route::get('/reports/all', [ReportController::class, 'index']);
    Route::post('/reports/add', [ReportController::class, 'store']);
    Route::post('/reports/delete', [ReportController::class, 'destroy']);

    // doctor
    Route::post('Schedules_doctor/add', [\App\Http\Controllers\Doctor\SchedulesController::class, 'addSchedules']);
    Route::post('Schedules_doctor/update/{id}', [\App\Http\Controllers\Doctor\SchedulesController::class, 'UpdataSchedules']);
    Route::post('Schedules_doctor/delete/{id}', [\App\Http\Controllers\Doctor\SchedulesController::class, 'delete']);
    Route::get('Schedules_doctor/show/{clinic_id}', [\App\Http\Controllers\Doctor\SchedulesController::class, 'getSchedulesByClinicId']);

    Route::post("/doctor/Presence",[PresenceController::class, 'Presence']);
});

Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/create', [RegisterController::class, 'Registeradmin']);


Route::post('auth/google', [GoogleAuth::class, 'GoogleLogin']);
Route::get('Clinics/nearby', [ClinicController::class, 'nearbyClinics']);
Route::get("Clinics/all", [ClinicController::class, "allClinics"]);
Route::get('/clinics/search', [ClinicController::class, 'searchClinics']);
Route::get('clinics/{id}', [ClinicController::class, 'showClinic']);
Route::get("Clinics/map", [ClinicController::class, "ClinicMap"]);

Route::get("Clinics/all/conferm", [ClinicConfermController::class, "allClinicsNotConferm"]);
Route::get('/Clinics/search/conferm', [ClinicConfermController::class, 'searchClinicsNotConferm']);


Route::post('Clinics/login', [Login::class, 'login']);
Route::post('Clinics/register', [Register::class, 'register']);


Route::get('Specialty/show', [showSpecialtyController::class, 'index']);



Route::get('Municipality/show', [MunicipalityController::class, 'show']);



Route::post('/doctor/login', [\App\Http\Controllers\Doctor\Auth\LoginController::class, 'login']);





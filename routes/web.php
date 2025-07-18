<?php

use App\Http\Controllers\Dashbaord\Admin\Admincontroller;
use App\Http\Controllers\Dashbaord\authentications\LoginBasic;
use App\Http\Controllers\Dashbaord\authentications\LogoutBasic;
use App\Http\Controllers\Dashbaord\authentications\RegisterBasic;
use App\Http\Controllers\Dashbaord\dashboard\Analytics;
use App\Http\Controllers\Dashbaord\Notification\NotificationController;
use App\Http\Controllers\Dashbaord\Report\Reportcontroller;
use App\Http\Controllers\Dashbaord\User\Switchcontroller;
use App\Http\Controllers\Dashbaord\User\Usercontroller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$controller_path = 'App\Http\Controllers';

Route::group(['middleware' => ['auth']], function () {
    Route::get('/theme/{theme}', function ($theme) {
        Session::put('theme', $theme);
        return redirect()->back();
    });

    Route::get('/lang/{lang}', function ($lang) {
        Session::put('locale', $lang);
        App::setLocale($lang);
        return redirect()->back();
    });
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [Analytics::class, "index"])->name('dashboard-analytics');
    Route::get('/dashboard/user-stats', [Analytics::class, 'userStats'])->name("dashboard.user-stats");
    Route::post('/nisab/update', [Analytics::class, 'updateNisab'])->name('nisab.update');
    Route::get('/user/index', [Usercontroller::class, "index"])->name('user-index');
    Route::get('/user/list', [Usercontroller::class, "list"])->name('user-list');
    Route::post('/user/Activation/{id}', [Switchcontroller::class, 'Activation']);
    Route::post('/users/{id}/make-experiment', [Switchcontroller::class, 'makeExperiment'])->name('users.make-experiment');
    Route::post('/user/delete', [UserController::class, 'delete']);
    Route::get('/admin/index', [Admincontroller::class, "index"])->name('admin-index');
    Route::get('/admin/list', [Admincontroller::class, "list"])->name('admin-list');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::post('/admin/get', [AdminController::class, 'get'])->name('admin.get');
    Route::post('/admin/update', [AdminController::class, 'update'])->name('admin.update');
    Route::get('/Report/index', [Reportcontroller::class, "index"])->name('Report-index');
    Route::get('/Report/list', [Reportcontroller::class, "list"])->name('Report-list');
    Route::post('/Report/delete/{id}', [ReportController::class, 'destroy'])->name('report.delete');
    Route::get('/Notification/index', [Notificationcontroller::class, "index"])->name('Notification-index');
    Route::get('/Notification/list', [NotificationController::class, "list"])->name('Notification-list');
    Route::post('/Notification/Create', [NotificationController::class, 'createNotification'])->name("Notification-Send");
    Route::post('/Notification/delete/{id}', [NotificationController::class, 'deleteNotification'])->name('Notification-delete');
    Route::post('/notification/resend', [NotificationController::class, 'resend'])->name('notification.resend');
    Route::get('/Notification/edit/{id}', [NotificationController::class, 'edit'])->name('Notification.edit');
    Route::post('/Notification/update', [NotificationController::class, 'update'])->name('Notification.update');

});




// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('login');
Route::get('/auth/register-basic', [RegisterBasic::class, "index"])->name('auth-registerbasic');
Route::post('/auth/register-action', [RegisterBasic::class, "register"])->name("register-action");
Route::post('/auth/login-action', [LoginBasic::class, "login"])->name("login-action");
// Route::get('/auth/forgot-password-basic', 'App\Http\Controllers\authentications\ForgotPasswordBasic@index')->name('auth-reset-password-basic');
Route::get('/auth/logout', [LogoutBasic::class,"Logout"])->name('auth-logout');


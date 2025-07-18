<?php

use App\Http\Controllers\admin\Auth\LoginController;
use App\Http\Controllers\admin\Auth\LogoutController;
use App\Http\Controllers\admin\Auth\RegisterController;
use App\Http\Controllers\User\Transaction\ShwoTransactionController;
use App\Http\Controllers\User\Auth\LogoutUserController;
use App\Http\Controllers\User\Auth\Password\NewPasswordController;
use App\Http\Controllers\User\Auth\Password\RessetpasswordController;
use App\Http\Controllers\User\Auth\Password\sendemaileController;
use App\Http\Controllers\User\Auth\Password\VerifyemailController;
use App\Http\Controllers\User\Auth\UpdateUserController;
use App\Http\Controllers\User\Categorie\AddCategorisController;
use App\Http\Controllers\User\Categorie\DeleteCategorisController;
use App\Http\Controllers\User\Categorie\EditCategorisController;
use App\Http\Controllers\User\Categorie\ShowCategorisController;
use App\Http\Controllers\User\Invoies\AddInvoiesController;
use App\Http\Controllers\User\Invoies\DeleteInvoiesController;
use App\Http\Controllers\User\Invoies\EditInvoiesController;
use App\Http\Controllers\User\Invoies\ShwoInvoiesController;
use App\Http\Controllers\User\Notification\deleteNotification;
use App\Http\Controllers\User\Notification\ShwoNotification;
use App\Http\Controllers\User\Product\AddProductController;
use App\Http\Controllers\User\Product\DeleteProductController;
use App\Http\Controllers\User\Product\EditProductController;
use App\Http\Controllers\User\Product\SearchProductController;
use App\Http\Controllers\User\Product\ShwoProductController;
use App\Http\Controllers\User\Report\AddReportController;
use App\Http\Controllers\User\Report\DeleteReportController;
use App\Http\Controllers\User\Report\ShwoReportController;
use App\Http\Controllers\User\Report\UpdateReportController;
use App\Http\Controllers\User\Transaction\AddTransactionController;
use App\Http\Controllers\User\Transaction\DeleteTransactionController;
use App\Http\Controllers\User\Transaction\EditTransactionController;
use App\Http\Controllers\User\Zakat\ShwoZakatController;

use Illuminate\Support\Facades\Route;




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/User/logout', [LogoutUserController::class, 'logout']);
    Route::post('/User/update', [UpdateUserController::class, 'UpdateUser']);

    Route::post('/User/resetpassword', [RessetpasswordController::class, 'reset']);


    Route::get('/Notification', [ShwoNotification::class, 'index']);
    Route::post('/Notification/shwo', [ShwoNotification::class, 'show']);
    Route::post('/Notification/delete', [deleteNotification::class, 'deletenot']);



    // admin






    // user_normal

    //categoris

    Route::get('/categories', [ShowCategorisController::class, 'index']);
    Route::post('/categories/show', [ShowCategorisController::class, 'show']);
    Route::post('/categories/create', [AddCategorisController::class, 'AddCategoris']);
    Route::post('/categories/update', [EditCategorisController::class, 'updateCategoris']);
    Route::post('/categories/delete', [DeleteCategorisController::class, 'destroyCategoris']);
    //Product
    Route::post('/products/create', [AddProductController::class, 'AddProduct']);
    Route::post('/products/update', [EditProductController::class, 'update']);
    Route::post('/products/Switch', [EditProductController::class, 'SwitchProduct']);

    Route::post('/products/delete', [DeleteProductController::class, 'delete']);
    Route::get('/products', [ShwoProductController::class, 'index']);
    Route::post('/products/show', [ShwoProductController::class, 'show']);
    Route::post('/products/show_cat', [ShwoProductController::class, 'ShowProdact']);
    Route::post('/products/show_by_cat', [ShwoProductController::class, 'ShowProdactbyCat']);

    Route::get('/products/Zakat/show', [ShwoProductController::class, 'ShowProducts_zakat']);
    Route::post('/products/search', [SearchProductController::class, 'search']);

    // Report
    Route::get('/Report', [ShwoReportController::class, 'index']);
    Route::post('/Report/show', [ShwoReportController::class, 'show']);
    Route::post('/Report/create', [AddReportController::class, 'AddReport']);
    Route::post('/Report/update', [UpdateReportController::class, 'Update']);
    Route::post('/Report/delete', [DeleteReportController::class, 'Delete']);

    Route::post('invoice/add', [AddInvoiesController::class, 'addInvoice']);
    Route::post('invoice/delete', [DeleteInvoiesController::class, 'deleteInvoice']);
    Route::post('invoice/update', [EditInvoiesController::class, 'updateInvoice']);
    Route::post('invoice/switch', [EditInvoiesController::class, 'updateInvoiceSwitch']);
    Route::post('invoice/by-transaction', [ShwoInvoiesController::class, 'getMyInvoicesByTransaction']);
    Route::post('invoice/show', [ShwoInvoiesController::class, 'showInvoice']);

    Route::post('transactions/add', [AddTransactionController::class, 'create']);
    Route::post('transactions/update', [EditTransactionController::class, 'update']);
    Route::post('transactions/delete', [DeleteTransactionController::class, 'delete']);
    Route::post('transactions/by-type', [ShwoTransactionController::class, 'getByType']);
    Route::post('transactions/Switch', [ShwoTransactionController::class, 'Switch']);

    Route::post('/Zakat/addCashliquidity', [ShwoZakatController::class, 'addCashliquidity']);
    Route::get('/Zakat', [ShwoZakatController::class, 'index']);
});


Route::post('/User/create', [\App\Http\Controllers\User\Auth\RegisterController::class, "RegisterUser"]);
Route::post('/User/Login', [\App\Http\Controllers\User\Auth\LoginUserController::class, "login"]);


Route::post('/User/sendCode', [sendemaileController::class, 'sendCode']);
Route::post('/User/verifyCode', [VerifyemailController::class, 'verifyCode']);
Route::post('/User/newpassword', [NewPasswordController::class, 'newpassword']);



Route::get('/test-firebase', function () {
    $messaging = app('firebase.messaging');
    dd($messaging);
});

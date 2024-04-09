<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\userController;
use App\Http\Controllers\api\orderController;
use App\Http\Controllers\api\studentController;
use App\Http\Controllers\api\paymentController;
use App\Http\Controllers\api\billController;
use App\Http\Controllers\api\majorController;
use App\Http\Controllers\api\historyController;
use App\Http\Controllers\api\frontendController;
use App\Http\Controllers\api\infoController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('api')->group(function (){
    route::prefix('user')->group(function(){
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('user',[userController::class,'user']);
            route::get('logout',[userController::class,'logout']);
            });
    });

    route::prefix('student')->group(function(){
        route::get('students',[studentController::class,'getStudent']);
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('{student:id}',[studentController::class,'showStudent']);
            route::post('{student:id}',[studentController::class,'setStudent']);
            route::post('delete/{student:id}',[studentController::class,'dropStudent']);
            route::post('create/student',[studentController::class,'createStudent']);
        });
        route::get('export/exel',[studentController::class,'exportExel']);
    });
    route::prefix('bill')->group(function(){
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('bills',[billController::class,'getBill']);
            route::get('data-bill',[billController::class,'bill']);
            route::get('{bill:id}',[billController::class,'showBill']);
            route::post('create',[billController::class,'createBill']);
            route::post('{bill:id}',[billController::class,'setBill']);
            route::post('hapus/bill/{bill:id}',[billController::class,'dropBill']);
        });
        route::get('export/excel-bill',[billController::class,'excelBill']);
    });
    route::prefix('major')->group(function(){
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('majors',[majorController::class,'getMajor']);
            route::get('data-major',[majorController::class,'major']);
            route::get('{major:id}',[majorController::class,'showMajor']);
            route::post('create',[majorController::class,'createMajor']);
            route::post('{major:id}',[majorController::class,'setMajor']);
            route::post('delete/{major:id}',[majorController::class,'dropMajor']);
        });
        route::get('export/excel-major',[majorController::class,'excelMajor']);
    });
    route::prefix('order')->group(function(){
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('{student:id}',[orderController::class,'showOrder']);
            route::get('{student:id}/all',[orderController::class,'allOrder']);
            route::post('{student:id}',[orderController::class,'create']);
            route::get('show/{order:id}',[orderController::class,'order']);
            route::post('set/{order:id}',[orderController::class,'setOrder']);
            route::post('delete/{order:id}',[orderController::class,'dropOrder']);
        });
        route::get('export/excel-order',[orderController::class,'excelOrder']);
    });
    route::prefix('history')->group(function(){
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('histories',[historyController::class,'getHistory']);
            route::get('{student:id}',[historyController::class,'showHistory']);
            route::get('{history:id}',[historyController::class,'history']);
            route::post('delete/{history:id}',[historyController::class,'dropHistory']);
        });
        route::get('export/excel-history',[historyController::class,'excelHistory']);
    });
    
    route::prefix('payment')->group(function(){
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('payments',[paymentController::class,'getPayment']);
            route::get('{student:id}',[paymentController::class,'showPayment']);
        });
    });
    
    route::prefix('frontend')->group(function(){
        Route::middleware(['auth:sanctum'])->group(function () {
            route::get('user/{id}',[studentController::class,'auth']);
            route::post('student/{student:id}',[frontendController::class,'setting']);
            route::post('delete/{history:id}',[frontendController::class,'dropHistory']);
            route::get('order/{student:id}',[frontendController::class,'showOrder']);
            route::get('history/{student:id}',[frontendController::class,'showHistory']);
            route::get('history/all/{student:id}',[frontendController::class,'historyAll']);
        });
        route::post('student-payment',[paymentController::class,'pay']);
        route::post('payment',[paymentController::class,'mid']);
        route::post('pesan-midtrans',[paymentController::class,'pesanMidtrans']);
        
        route::get('xendit',[paymentController::class,'xenditPay']);
        route::post('create-xendit',[paymentController::class,'createXendit']);
        route::post('pesan-xendit',[paymentController::class,'pesanXendit']);
        route::post('callback-xendit',[paymentController::class,'callback']);
        route::post('callback-expired-xendit',[paymentController::class,'expired']);
        route::post('callback-paid-xendit',[paymentController::class,'paid']);
        route::get('xendit/{id}',[paymentController::class,'getXenditPay']);
        route::post('xendit/cencel/{id}',[paymentController::class,'cencelXenditPay']);
    });
    

    
    route::post('reset-password',[userController::class,'setPass']);
    
    route::get('info',[infoController::class,'get']);
    route::post('info/set/{info:id}',[infoController::class,'setInfo']);
    route::post('info/delete/{info:id}',[infoController::class,'dropInfo']);
    route::post('create/info',[infoController::class,'createInfo']);
   

    route::get('pengumuman',[infoController::class,'getPengumuman']);
    route::post('pengumuman/set/{id}',[infoController::class,'setPengumuman']);
    route::post('pengumuman/delete/{id}',[infoController::class,'dropPengumuman']); 
    route::post('create/pengumuman',[infoController::class,'createPengumuman']);
    
    route::post('cencel-payment',[paymentController::class,'cencel']);
    route::post('handle-payment',[paymentController::class,'set']);
    route::post('upload',[paymentController::class,'foto']);
    route::post('login',[userController::class,'login']);

    
});






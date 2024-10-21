<?php

use Illuminate\Http\Request;
use App\Http\Middleware\AblePayOrder;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AbleCreateUser;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AbleCreateOrder;
use App\Http\Middleware\AbleFinishOrder;
use App\Http\Middleware\AbleOrderReport;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\AbleCreateUpdateItem;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']); //di postman jadi api/auth/
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

});

Route::middleware(['auth:sanctum'])->group(function () {

    // Route::post('create-order',function(){
    //     return 'create-order';
    //     })->middleware([AbleCreateOrder::class]);
    
    // Route::post('finish-order',function(){
    //     return 'finish-order';
    //     })->middleware([AbleFinishOrder::class]);

    Route::post('user',[UserController::class, 'store'])
        ->middleware([AbleCreateUser::class]);
    
    
    Route::get('item',[ItemController::class, 'index']);
    Route::get('item/{item}',[ItemController::class, 'show']);
    Route::post('item',[ItemController::class, 'store'])
        ->middleware([AbleCreateUpdateItem::class]);
    Route::patch('item/{item}',[ItemController::class, 'update'])
        ->middleware([AbleCreateUpdateItem::class]);

    Route::get('order', [OrderController::class,'index']);
    Route::get('order/{order}', [OrderController::class,'show']);
    Route::get('/order/{order}/set-as-done',[OrderController::class, 'setAsDone'])
        ->middleware([AbleFinishOrder::class]);
    Route::get('/order/{order}/payment',[OrderController::class, 'payment'])
        ->middleware([AblePayOrder::class]);
    Route::post('order',[OrderController::class,'store'])
        ->middleware([AbleCreateOrder::class]);
    Route::get('order-report', [OrderController::class,'orderReport'])
        ->middleware([AbleOrderReport::class]);
    
    

    
    
});




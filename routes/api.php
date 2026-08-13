<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categories', CategoryController::class);

Route::apiResource('products', ProductController::class);

Route::get('getCateory',[ProductController::class,'getCategory']);

Route::post('/products/{product}',[ProductController::class,'update']);

Route::apiResource('users',AuthController::class);

Route::post('/sendOtp',[AuthController::class,'sendOtp']);

Route::post('/verifyOtp',[AuthController::class,'verifyOtp']);

Route::post('/register',[AuthController::class,'register']);

// Route::middleware('auth:sanctum')->get('/profile',[AuthController::class,'profile']);

Route::middleware('auth:sanctum')->group(function(){

    Route::get('/profile',[AuthController::class,'profile']);
   
});
 Route::post('/login',[AuthController::class,'login']);
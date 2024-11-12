<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\API\BusinessInformationController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::post('password/forgot',[ForgotPasswordController::class,'forgotPassword']);
Route::post('password/reset',[ResetPasswordController::class,'resetPassword']);

// Route::middleware('auth:sanctum')->put('/profile', [ProfileController::class, 'update']);
Route::patch('/profile', [ProfileController::class, 'update'])->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->put('profile/update', [ProfileController::class, 'update']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/business-information', [BusinessInformationController::class, 'index']);
    Route::post('/business-information', [BusinessInformationController::class, 'store']);
    Route::get('/business-information/{id}', [BusinessInformationController::class, 'show']);
    Route::put('/business-information/{id}', [BusinessInformationController::class, 'update']);
    Route::delete('/business-information/{id}', [BusinessInformationController::class, 'destroy']);
});


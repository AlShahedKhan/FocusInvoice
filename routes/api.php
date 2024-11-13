<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\API\ConsignmentController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;

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


Route::get('/consignments', [ConsignmentController::class, 'index']);
Route::post('/consignments', [ConsignmentController::class, 'store']);
Route::get('/consignments/{id}', [ConsignmentController::class, 'show']);
Route::put('/consignments/{consignment}', [ConsignmentController::class, 'update']);
Route::delete('/consignments/{consignment}', [ConsignmentController::class, 'destroy']);

// Route::middleware('auth:sanctum')->put('/profile', [ProfileController::class, 'update']);
Route::patch('/profile', [ProfileController::class, 'update'])->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->put('profile/update', [ProfileController::class, 'update']);



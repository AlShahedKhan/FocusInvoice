<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\PayPalController;
use App\Http\Controllers\InviteCodeController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\API\ConsignmentController;
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


Route::get('/consignments', [ConsignmentController::class, 'index']);
Route::post('/consignments', [ConsignmentController::class, 'store']);
Route::get('/consignments/{consignment}', [ConsignmentController::class, 'show']);
Route::put('/consignments/{consignment}', [ConsignmentController::class, 'update']);
Route::delete('/consignments/{consignment}', [ConsignmentController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile/profile', [ProfileController::class, 'update']);
    Route::get('/profile/profile', [ProfileController::class, 'show']);
    Route::delete('/profile/profile', [ProfileController::class, 'deleteAccount']);  // Delete profile
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/invite-code/generate', [InviteCodeController::class, 'generateInviteCode']); // Generate invite code (Admin only)
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile/change-password', [ChangePasswordController::class, 'updatePassword']);
});



// Route::middleware('auth:sanctum')->put('profile/update', [ProfileController::class, 'update']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/business-information', [BusinessInformationController::class, 'index']);
    Route::post('/business-information', [BusinessInformationController::class, 'store']);
    Route::get('/business-information/{id}', [BusinessInformationController::class, 'show']);
    Route::put('/business-information/{id}', [BusinessInformationController::class, 'update']);
    Route::delete('/business-information/{id}', [BusinessInformationController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('paypal/create-order', [PayPalController::class, 'createOrder']);
    Route::post('paypal/capture-order', [PayPalController::class, 'captureOrder'])->name('paypal.capture-order');
    Route::get('paypal/cancel-order', [PayPalController::class, 'cancelOrder'])->name('paypal.cancel-order');
});

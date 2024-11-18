<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PayPalController;
use App\Http\Controllers\InviteCodeController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\API\ConsignmentController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\API\BusinessInformationController;

// Authentication routes
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth.jwt'); // Use JWT middleware here
});


// Password management
Route::post('password/forgot', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('password/reset', [ResetPasswordController::class, 'resetPassword']);

Route::middleware('auth.jwt')->group(function () {
    Route::prefix('profile')->group(function () {
        Route::post('update', [ProfileController::class, 'update']);
        Route::get('show', [ProfileController::class, 'show']);
        Route::delete('delete', [ProfileController::class, 'deleteAccount']);
        Route::post('change-password', [ChangePasswordController::class, 'updatePassword']);
    });

    Route::post('invite-code/generate', [InviteCodeController::class, 'generateInviteCode']);

    // Business Information routes
    // Route::prefix('business-information')->group(function () {
    //     Route::get('/', [BusinessInformationController::class, 'index']);
    //     Route::post('/', [BusinessInformationController::class, 'store']);
    //     Route::get('{id}', [BusinessInformationController::class, 'show']);
    //     Route::put('{id}', [BusinessInformationController::class, 'update']);
    //     Route::delete('{id}', [BusinessInformationController::class, 'destroy']);
    // });

     // PayPal routes
    //  Route::prefix('paypal')->group(function () {
    //     Route::post('create-order', [PayPalController::class, 'createOrder']);
    //     Route::post('capture-order', [PayPalController::class, 'captureOrder'])->name('paypal.capture-order');
    //     Route::get('cancel-order', [PayPalController::class, 'cancelOrder'])->name('paypal.cancel-order');
    // });
});
Route::middleware('auth.jwt')->prefix('business-information')->group(function () {
    Route::get('/', [BusinessInformationController::class, 'index']);
    Route::post('/', [BusinessInformationController::class, 'store']);
    Route::get('{id}', [BusinessInformationController::class, 'show']);
    Route::put('{id}', [BusinessInformationController::class, 'update']);
    Route::delete('{id}', [BusinessInformationController::class, 'destroy']);
});
Route::middleware('auth.jwt')->prefix('paypal')->group(function () {
    Route::post('create-order', [PayPalController::class, 'createOrder']);
    Route::post('capture-order', [PayPalController::class, 'captureOrder'])->name('paypal.capture-order');
    Route::get('cancel-order', [PayPalController::class, 'cancelOrder'])->name('paypal.cancel-order');
});


// Protected routes (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // Profile routes
    // Route::prefix('profile')->group(function () {
    //     Route::post('update', [ProfileController::class, 'update']);
    //     Route::get('show', [ProfileController::class, 'show']);
    //     Route::delete('delete', [ProfileController::class, 'deleteAccount']);
    //     Route::post('change-password', [ChangePasswordController::class, 'updatePassword']);
    // });

    // // Invite Code routes (Admin only)
    // Route::post('invite-code/generate', [InviteCodeController::class, 'generateInviteCode']);



    // // PayPal routes
    // Route::prefix('paypal')->group(function () {
    //     Route::post('create-order', [PayPalController::class, 'createOrder']);
    //     Route::post('capture-order', [PayPalController::class, 'captureOrder'])->name('paypal.capture-order');
    //     Route::get('cancel-order', [PayPalController::class, 'cancelOrder'])->name('paypal.cancel-order');
    // });
});

// Consignment routes
Route::prefix('consignments')->group(function () {
    Route::get('/', [ConsignmentController::class, 'index']);
    Route::post('/', [ConsignmentController::class, 'store']);
    Route::get('{consignment}', [ConsignmentController::class, 'show']);
    Route::put('{consignment}', [ConsignmentController::class, 'update']);
    Route::delete('{consignment}', [ConsignmentController::class, 'destroy']);
});

// Decode Token route
Route::get('/decode-token', [TokenController::class, 'decodeToken']);

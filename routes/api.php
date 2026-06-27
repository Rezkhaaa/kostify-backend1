<?php

use App\Http\Controllers\API\{
    AuthController,
    UnitController,
    BookingController,
    BillingController,
    PaymentController,
    ComplaintController,
    MaintenanceController,
    HistoryController,
    PropertyController,
    NotificationController,
    MediaController
};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/google', [AuthController::class, 'googleLogin']);
    Route::post('/auth/password/send-code', [AuthController::class, 'sendResetCode']);
    Route::post('/auth/password/verify-code', [AuthController::class, 'verifyResetCode']);
    Route::post('/auth/password/reset', [AuthController::class, 'resetPasswordWithCode']);
});

Route::prefix('v1')->middleware(['auth:sanctum', 'active_mobile_tenant'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'changePassword']);
    Route::post('/auth/account-deletion', [AuthController::class, 'requestAccountDeletion']);

    Route::get('/notifications/config', [NotificationController::class, 'config']);
    Route::post('/notifications/device', [NotificationController::class, 'storeDevice']);
    Route::post('/notifications/toggle', [NotificationController::class, 'toggle']);
    Route::get('/notifications/count', [NotificationController::class, 'count']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead']);

    Route::get('/media/payments/{payment}/proof', [MediaController::class, 'paymentProof']);
    Route::get('/media/complaints/{complaint}/photo', [MediaController::class, 'complaintPhoto']);
    Route::get('/media/maintenances/{maintenance}/photo', [MediaController::class, 'maintenancePhoto']);

    Route::get('/properties', [PropertyController::class, 'index']);

    Route::get('/units', [UnitController::class, 'index']);
    Route::get('/units/{unit}', [UnitController::class, 'show']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);

    Route::get('/billings', [BillingController::class, 'index']);
    Route::get('/billings/{billing}', [BillingController::class, 'show']);
    Route::get('/payments/manual-info', [PaymentController::class, 'manualInfo']);
    Route::post('/billings/{billing}/pay', [PaymentController::class, 'pay']);

    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);

    Route::get('/maintenances', [MaintenanceController::class, 'index']);
    Route::post('/maintenances', [MaintenanceController::class, 'store']);
    Route::get('/maintenances/{maintenance}', [MaintenanceController::class, 'show']);

    Route::get('/history', [HistoryController::class, 'index']);
    Route::get('/history/bookings', [HistoryController::class, 'bookings']);
    Route::get('/history/payments', [HistoryController::class, 'payments']);
});

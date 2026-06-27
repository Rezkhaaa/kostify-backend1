<?php

use App\Http\Controllers\Web\{
    AuthController,
    BillingController,
    BookingController,
    ComplaintController,
    DashboardController,
    MaintenanceController,
    MediaController,
    NotificationLogController,
    OwnerRegistrationController,
    PaymentController,
    PaymentSettingController,
    PasswordResetRequestController,
    ProfileController,
    PropertyAdminController,
    PropertyController,
    RegistrationRequestController,
    TenantController,
    TenantRegistrationController,
    UnitController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.login'));
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/account-deletion', 'account-deletion')->name('account-deletion');

Route::prefix('admin')->name('admin.')->group(function () {
    // Login dan pendaftaran pemilik kos dibuat publik agar tidak terjadi redirect loop
    // ketika browser masih menyimpan session user lama/non-admin.
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
    Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Pendaftaran ini khusus calon pemilik kos/Admin Kos. Akun tidak langsung aktif,
    // tetapi menunggu approval dari Super Admin.
    Route::get('/daftar-pemilik-kos', [OwnerRegistrationController::class, 'create'])->name('owner-register.create');
    Route::post('/daftar-pemilik-kos', [OwnerRegistrationController::class, 'store'])->name('owner-register.store');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/notifications', [NotificationLogController::class, 'index'])->name('notifications.index');

        Route::get('/media/payments/{payment}/proof', [MediaController::class, 'paymentProof'])->name('media.payments.proof');
        Route::get('/media/complaints/{complaint}/photo', [MediaController::class, 'complaintPhoto'])->name('media.complaints.photo');
        Route::get('/media/maintenances/{maintenance}/photo', [MediaController::class, 'maintenancePhoto'])->name('media.maintenances.photo');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::get('/payment-settings', [PaymentSettingController::class, 'edit'])->name('payment-settings.edit');
        Route::post('/payment-settings', [PaymentSettingController::class, 'update'])->name('payment-settings.update');

        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::match(['post', 'patch'], '/tenants/{tenant}/toggle', [TenantController::class, 'toggleStatus'])->name('tenants.toggle');
        Route::match(['post', 'patch'], '/tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');

        Route::middleware('super_admin')->group(function () {
            Route::resource('properties', PropertyController::class)->except(['show', 'destroy']);
            Route::post('/properties/{property}/toggle', [PropertyController::class, 'toggle'])->name('properties.toggle');

            Route::get('/property-admins', [PropertyAdminController::class, 'index'])->name('property-admins.index');
            Route::get('/property-admins/create', [PropertyAdminController::class, 'create'])->name('property-admins.create');
            Route::post('/property-admins', [PropertyAdminController::class, 'store'])->name('property-admins.store');
            Route::get('/property-admins/{propertyAdmin}/edit', [PropertyAdminController::class, 'edit'])->name('property-admins.edit');
            Route::put('/property-admins/{propertyAdmin}', [PropertyAdminController::class, 'update'])->name('property-admins.update');
            Route::post('/property-admins/{propertyAdmin}/toggle', [PropertyAdminController::class, 'toggle'])->name('property-admins.toggle');

            Route::get('/registrations', [RegistrationRequestController::class, 'index'])->name('registrations.index');
            Route::post('/registrations/{registrationRequest}/approve', [RegistrationRequestController::class, 'approve'])->name('registrations.approve');
            Route::post('/registrations/{registrationRequest}/reject', [RegistrationRequestController::class, 'reject'])->name('registrations.reject');

            Route::get('/tenant-registrations', [TenantRegistrationController::class, 'index'])->name('tenant-registrations.index');
            Route::post('/tenant-registrations/{tenantRegistration}/approve', [TenantRegistrationController::class, 'approve'])->name('tenant-registrations.approve');
            Route::post('/tenant-registrations/{tenantRegistration}/reject', [TenantRegistrationController::class, 'reject'])->name('tenant-registrations.reject');

            Route::get('/password-resets', [PasswordResetRequestController::class, 'index'])->name('password-resets.index');
            Route::post('/password-resets/users/{user}/send', [PasswordResetRequestController::class, 'sendForUser'])->name('password-resets.send');
            Route::post('/password-resets/{passwordResetRequest}/reject', [PasswordResetRequestController::class, 'reject'])->name('password-resets.reject');
        });

        Route::middleware('property_admin')->group(function () {
            Route::resource('units', UnitController::class);

            Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');

        Route::get('/billings', [BillingController::class, 'index'])->name('billings.index');
        Route::get('/billings/create', [BillingController::class, 'create'])->name('billings.create');
        Route::post('/billings', [BillingController::class, 'store'])->name('billings.store');
        Route::get('/billings/{billing}', [BillingController::class, 'show'])->name('billings.show');
        Route::post('/billings/{billing}/verify', [BillingController::class, 'verify'])->name('billings.verify');
        Route::delete('/billings/{billing}', [BillingController::class, 'destroy'])->name('billings.destroy');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
        Route::match(['put', 'patch'], '/complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update');
        Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');

        Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
            Route::get('/maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
            Route::match(['put', 'patch'], '/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
            Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');
        });
    });
});

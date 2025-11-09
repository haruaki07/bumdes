<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessRegistrationController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'));
Auth::routes(['verify' => true]);

Route::middleware(['auth'])->group(function () {
    // Profile Completion
    Route::get('/profile/complete', [\App\Http\Controllers\ProfileController::class, 'complete'])->name('profile.complete');
    Route::post('/profile/complete', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/warga', [\App\Http\Controllers\ProfileController::class, 'updateWargaProfile'])->name('profile.warga.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Business Registration Management (requires verified email and completed profile)
    Route::middleware(['profile.completed'])->group(function () {
        Route::resource('business-registrations', BusinessRegistrationController::class)->except(['edit', 'update']);
        Route::post('/business-registrations/{businessRegistration}/approve', [BusinessRegistrationController::class, 'approve'])->name('business-registrations.approve');
        Route::post('/business-registrations/{businessRegistration}/reject', [BusinessRegistrationController::class, 'reject'])->name('business-registrations.reject');
        Route::get('/business-registrations/{businessRegistration}/revise', [BusinessRegistrationController::class, 'revise'])->name('business-registrations.revise');
        Route::post('/business-registrations/{businessRegistration}/revise', [BusinessRegistrationController::class, 'storeRevision'])->name('business-registrations.store-revision');
    });

    // Business Management
    Route::resource('businesses', BusinessController::class);
    Route::resource('business-types', BusinessTypeController::class);
    Route::put('/business-types/{id}/restore', [BusinessTypeController::class, 'restore'])->name('business-types.restore');
    Route::delete('/business-types/{id}/destroy-trashed', [BusinessTypeController::class, 'destroyTrashed'])->name('business-types.destroy-trashed');

    // User Management
    Route::resource('users', UserController::class);

    // Role Management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

    // Funding Management
    Route::middleware(['profile.completed'])->group(function () {
        Route::resource('funding-requests', \App\Http\Controllers\FundingRequestController::class);
        Route::post('/funding-requests/{fundingRequest}/approve', [\App\Http\Controllers\FundingRequestController::class, 'approve'])->name('funding-requests.approve');
        Route::post('/funding-requests/{fundingRequest}/reject', [\App\Http\Controllers\FundingRequestController::class, 'reject'])->name('funding-requests.reject');
        Route::post('/funding-requests/{fundingRequest}/upload-mou', [\App\Http\Controllers\FundingRequestController::class, 'uploadMou'])->name('funding-requests.upload-mou');
        Route::post('/funding-requests/{fundingRequest}/sign-mou', [\App\Http\Controllers\FundingRequestController::class, 'signMou'])->name('funding-requests.sign-mou');
        Route::post('/funding-requests/{fundingRequest}/disburse', [\App\Http\Controllers\FundingRequestController::class, 'disburse'])->name('funding-requests.disburse');
        Route::post('/funding-requests/{fundingRequest}/repayments', [\App\Http\Controllers\FundingRequestController::class, 'storeRepayment'])->name('funding-requests.repayments.store');
        Route::post('/repayments/{repayment}/verify', [\App\Http\Controllers\FundingRequestController::class, 'verifyRepayment'])->name('repayments.verify');
    });
});

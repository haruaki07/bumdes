<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessRegistrationController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FundingRequestController;
use App\Http\Controllers\LabaRugiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'));
Auth::routes(['verify' => true]);

Route::middleware(['auth'])->group(function () {
    // Profile Completion
    Route::get('/profile/complete', [ProfileController::class, 'complete'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/warga', [ProfileController::class, 'updateWargaProfile'])->name('profile.warga.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics'])->name('dashboard.metrics');

    // Business Registration Management
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
        Route::resource('funding-requests', FundingRequestController::class);
        Route::post('/funding-requests/{fundingRequest}/approve', [FundingRequestController::class, 'approve'])->name('funding-requests.approve');
        Route::post('/funding-requests/{fundingRequest}/reject', [FundingRequestController::class, 'reject'])->name('funding-requests.reject');
        Route::post('/funding-requests/{fundingRequest}/upload-mou', [FundingRequestController::class, 'uploadMou'])->name('funding-requests.upload-mou');
        Route::post('/funding-requests/{fundingRequest}/sign-mou', [FundingRequestController::class, 'signMou'])->name('funding-requests.sign-mou');
        Route::post('/funding-requests/{fundingRequest}/disburse', [FundingRequestController::class, 'disburse'])->name('funding-requests.disburse');
        Route::post('/funding-requests/{fundingRequest}/repayments', [FundingRequestController::class, 'storeRepayment'])->name('funding-requests.repayments.store');
        Route::post('/repayments/{repayment}/verify', [FundingRequestController::class, 'verifyRepayment'])->name('repayments.verify');
    });

    // Laba Rugi (Profit & Loss Report)
    Route::prefix('laba-rugi')->name('laba-rugi.')->group(function () {
        Route::get('/', [LabaRugiController::class, 'index'])->name('index');
        Route::post('/generate', [LabaRugiController::class, 'generate'])->name('generate');
        Route::post('/export-pdf', [LabaRugiController::class, 'exportPdf'])->name('export-pdf');
    });

    // Transaction Categories Management
    Route::resource('transaction-categories', TransactionCategoryController::class)->except(['show']);
    Route::post('/transaction-categories/{transactionCategory}/toggle-active', [TransactionCategoryController::class, 'toggleActive'])->name('transaction-categories.toggle-active');

    // Transactions Management
    Route::resource('transactions', TransactionController::class);
    Route::patch('/transactions/{transaction}/verify', [TransactionController::class, 'verify'])->name('transactions.verify');
    Route::patch('/transactions/{transaction}/unverify', [TransactionController::class, 'unverify'])->name('transactions.unverify');

    // Backup & Restore
    Route::name('admin.')->group(function () {
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::get('/backups/create', [BackupController::class, 'create'])->name('backups.create');
        Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
        Route::delete('/backups/clean', [BackupController::class, 'clean'])->name('backups.clean');
        Route::post('/backups/clean-old', [BackupController::class, 'cleanOld'])->name('backups.clean-old');
        Route::get('/backups/{backup}', [BackupController::class, 'show'])->name('backups.show');
        Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/{backup}/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
        Route::get('/backups-statistics', [BackupController::class, 'statistics'])->name('backups.statistics');
    });
});

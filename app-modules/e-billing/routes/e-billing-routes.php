<?php

// use Modules\EBilling\Http\Controllers\EBillingController;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Modules\EBilling\Http\Controllers\CustomerController;
use Modules\EBilling\Http\Controllers\DeviceController;
use Modules\EBilling\Http\Controllers\PackageController;
use Modules\EBilling\Http\Controllers\SiteController;

Route::middleware(['web'])->group(function () {
    Route::prefix('e-billing')->as('e-billing.')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::middleware(['auth_ebil'])->group(function () {
            Route::get('/', fn () => view('e-billing::dashboard'))->name('dashboard');
            Route::prefix('master-data')->as('master-data.')->group(function () {
                Route::resource('sites', SiteController::class);
                Route::resource('devices', DeviceController::class);
                Route::resource('packages', PackageController::class);
                Route::resource('customers', CustomerController::class);
            });
        });
    });
});

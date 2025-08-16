<?php

// use Modules\EBilling\Http\Controllers\EBillingController;

use Illuminate\Support\Facades\Route;
use Modules\EBilling\Http\Controllers\CustomerController;
use Modules\EBilling\Http\Controllers\DeviceController;
use Modules\EBilling\Http\Controllers\PackageController;
use Modules\EBilling\Http\Controllers\SiteController;

Route::middleware(['web'])->group(function () {
    Route::prefix('e-billing')->as('e-billing.')->middleware(['auth'])->group(function () {
        Route::get('/', fn() => view('e-billing::dashboard'))->name('dashboard');

        Route::prefix('master-data')->as('master-data.')->group(function () {
            Route::resource('sites', SiteController::class);
            Route::resource('devices', DeviceController::class);
            Route::resource('packages', PackageController::class);
            Route::resource('customers', CustomerController::class);
        });
    });
});

<?php

// use Modules\EBilling\Http\Controllers\EBillingController;

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::prefix('e-billing')->as('e-billing.')->middleware(['auth'])->group(function () {
        Route::get('/', fn() => view('e-billing::dashboard'))->name('dashboard');

        Route::prefix('master-data')->as('master-data.')->group(function () {
            Route::get('sites', fn() => view('e-billing::dashboard'))->name('sites.index');
            Route::get('devices', fn() => view('e-billing::dashboard'))->name('devices.index');
            Route::get('customers', fn() => view('e-billing::dashboard'))->name('customers.index');
        });
    });
});

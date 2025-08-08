<?php

// use Modules\EBilling\Http\Controllers\EBillingController;

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::prefix('e-billing')->as('e-billing.')->middleware(['auth'])->group(function () {
        Route::get('/', fn () => view('e-billing::dashboard'))->name('dashboard');
    });
});

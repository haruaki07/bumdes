<?php

// use Modules\EBilling\Http\Controllers\EBillingController;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Modules\EBilling\Http\Controllers\CustomerController;
use Modules\EBilling\Http\Controllers\DashboardController;
use Modules\EBilling\Http\Controllers\DeviceController;
use Modules\EBilling\Http\Controllers\InvoiceController;
use Modules\EBilling\Http\Controllers\PackageController;
use Modules\EBilling\Http\Controllers\PaymentMethodController;
use Modules\EBilling\Http\Controllers\SettingsController;
use Modules\EBilling\Http\Controllers\SiteController;
use Modules\EBilling\Http\Controllers\TransferReceiptController;
use Modules\EBilling\Http\Controllers\WebhookController;

Route::prefix('e-billing')->as('e-billing.')->group(function () {
    Route::middleware(['web'])->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/invoice', [InvoiceController::class, 'customerSearch'])->name('invoice.customer-search');
        Route::get('/invoice/pay', [InvoiceController::class, 'pay'])->name('invoice.pay');
        Route::get('/invoice/{customer_id}', [InvoiceController::class, 'customerShow'])->name('invoice.customer-show');

        Route::middleware(['auth_ebil'])->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/metrics', [DashboardController::class, 'metrics'])->name('dashboard.metrics');
            Route::prefix('master-data')->as('master-data.')->group(function () {
                Route::resource('sites', SiteController::class);
                Route::resource('devices', DeviceController::class);
                Route::resource('packages', PackageController::class);
                Route::resource('customers', CustomerController::class);
            });

            Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
            // Manual invoice creation (admin triggered)
            Route::post('/invoices/create-manual', [InvoiceController::class, 'createManual'])->name('invoices.create-manual');
            Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
            Route::post('/invoices/{invoice}/mark-unpaid', [InvoiceController::class, 'markUnpaid'])->name('invoices.mark-unpaid');

            Route::prefix('settings')->as('settings.')->group(function () {
                Route::resource('payment-methods', PaymentMethodController::class)->only(['index', 'show', 'update']);
                Route::patch('payment-methods/{paymentMethod}/status', [PaymentMethodController::class, 'updateStatus'])
                    ->name('payment-methods.update-status');

                Route::get('/', [SettingsController::class, 'index'])->name('index');
                Route::get('/{group}', [SettingsController::class, 'show'])->name('show');
                Route::put('/{group}', [SettingsController::class, 'update'])->name('update');
            });
        });
    });

    Route::middleware(['api'])->prefix('api')->group(function () {
        Route::post('/invoice/{customer_id}/request-payment', [InvoiceController::class, 'requestPayment'])
            ->name('invoice.request-payment');
        Route::get('/invoice/pay/status', [InvoiceController::class, 'payStatus'])
            ->name('invoice.pay.status');
        Route::post('/invoice/pay/simulate', [InvoiceController::class, 'simulatePayment'])
            ->name('invoice.pay.simulate');
        Route::delete('/invoice/{customer_id}/saved-payment-method', [InvoiceController::class, 'removeSavedPaymentMethod'])
            ->name('invoice.remove-saved-payment-method');
        Route::post('/invoice/{invoice}/transfer-receipts', [TransferReceiptController::class, 'store'])
            ->name('invoice.transfer-receipts.store');
        Route::post('/webhooks/xendit', [WebhookController::class, 'xendit'])
            ->name('webhooks.xendit');
    });
});

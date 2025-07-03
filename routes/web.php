<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessRegistrationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Business Registration Management
    Route::resource('business-registrations', BusinessRegistrationController::class)->except(['edit', 'update']);
    Route::post('/business-registrations/{businessRegistration}/approve', [BusinessRegistrationController::class, 'approve'])->name('business-registrations.approve');
    Route::post('/business-registrations/{businessRegistration}/reject', [BusinessRegistrationController::class, 'reject'])->name('business-registrations.reject');
    Route::get('/business-registrations/{businessRegistration}/revise', [BusinessRegistrationController::class, 'revise'])->name('business-registrations.revise');
    Route::post('/business-registrations/{businessRegistration}/revise', [BusinessRegistrationController::class, 'storeRevision'])->name('business-registrations.store-revision');

    // Business Management
    Route::resource('businesses', BusinessController::class);
    Route::post('/businesses/{business}/request-name-change', [BusinessController::class, 'requestNameChange'])->name('businesses.request-name-change');
    Route::post('/businesses/{business}/approve-name-change', [BusinessController::class, 'approveNameChange'])->name('businesses.approve-name-change');
    Route::post('/businesses/{business}/reject-name-change', [BusinessController::class, 'rejectNameChange'])->name('businesses.reject-name-change');
    // Route::resource('business-types', BusinessTypeController::class);

    // // Funding Management
    // Route::resource('funding-requests', FundingRequestController::class);
    // Route::post('/funding-requests/{funding_request}/approve', [FundingRequestController::class, 'approve'])->name('funding-requests.approve');
    // Route::post('/funding-requests/{funding_request}/reject', [FundingRequestController::class, 'reject'])->name('funding-requests.reject');
    // Route::post('/funding-requests/{funding_request}/disburse', [FundingRequestController::class, 'disburse'])->name('funding-requests.disburse');
    // Route::post('/funding-requests/{funding_request}/upload-mou', [FundingRequestController::class, 'uploadMou'])->name('funding-requests.upload-mou');
    // Route::post('/funding-requests/{funding_request}/approve-mou', [FundingRequestController::class, 'approveMou'])->name('funding-requests.approve-mou');

    // // Internet Service Management
    // Route::resource('internet-services', InternetServiceController::class);
    // Route::post('/internet-services/{internet_service}/suspend', [InternetServiceController::class, 'suspend'])->name('internet-services.suspend');
    // Route::post('/internet-services/{internet_service}/activate', [InternetServiceController::class, 'activate'])->name('internet-services.activate');
    // Route::resource('internet-payments', InternetPaymentController::class);

    // // SAMSAT Management
    // Route::resource('samsat-transactions', SamsatTransactionController::class);
    // Route::get('/samsat-reports/monthly', [SamsatTransactionController::class, 'monthlyReport'])->name('samsat-reports.monthly');

    // // Reports
    // Route::prefix('reports')->name('reports.')->group(function () {
    //   Route::get('/business', [DashboardController::class, 'businessReport'])->name('business');
    //   Route::get('/funding', [DashboardController::class, 'fundingReport'])->name('funding');
    //   Route::get('/internet', [DashboardController::class, 'internetReport'])->name('internet');
    //   Route::get('/samsat', [DashboardController::class, 'samsatReport'])->name('samsat');
    // });
});

Route::get('/hello', function () {
    return 'Hello World';
});

// Redirect root to dashboard for authenticated users
// Route::get('/', function () {
//   return redirect()->route('dashboard');
// })->middleware('auth');

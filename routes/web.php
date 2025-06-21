<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessController;

Route::get('/', fn() => view("welcome"));
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
  // Dashboard
  // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

  // Business Management
  Route::resource('businesses', BusinessController::class);
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

// Redirect root to dashboard for authenticated users
// Route::get('/', function () {
//   return redirect()->route('dashboard');
// })->middleware('auth');

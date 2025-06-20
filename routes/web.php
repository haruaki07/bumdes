<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view("welcome"));
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

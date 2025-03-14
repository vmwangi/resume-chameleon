<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResumeDownloadController;

// Auth routes
Route::get('/login/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/login/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/authenticated', function () {
    return view('welcome');
});

// Define a login route to prevent the "Route [login] not defined" error
Route::get('/login', function () {
    return redirect('/');
})->name('login');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\AdminsController;
use App\Http\Controllers\Backend\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\RolesController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\InsuranceConfirmationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    return view('test');
});

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RolesController::class);
    Route::resource('admins', AdminsController::class);

    // insurance_confirmations/import.blade.php
    Route::get('insurance-confirmations', [InsuranceConfirmationController::class, 'index'])->name('insurance-confirmations.view');
    Route::get('insurance-confirmations/import', [InsuranceConfirmationController::class, 'importView'])->name('insurance-confirmations.import-view');
    Route::post('insurance-confirmations/import', [InsuranceConfirmationController::class, 'import'])->name('insurance-confirmations.import');

    // Login Routes.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login/submit', [LoginController::class, 'login'])->name('login.submit');

    // Logout Routes.
    Route::post('/logout/submit', [LoginController::class, 'logout'])->name('logout.submit');

    // Forget Password Routes.
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/reset/submit', [ForgotPasswordController::class, 'reset'])->name('password.update');

    // my-routes
    Route::get('/view-json/{id}', [ApplicationController::class, 'viewJson'])->name('view.json');
    Route::delete('/applications/{id}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

})->middleware('auth:admin');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

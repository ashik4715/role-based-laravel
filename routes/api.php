<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationResubmissionController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('health', [HealthController::class, 'index']);

Route::controller(ApplicationController::class)->group(function () {
    Route::post('next', 'next')->middleware('auth.jwt');
    Route::get('application/step', 'getStepFromApplicationData')->middleware('auth.jwt');
    Route::get('application/review', 'getReviewStep')->middleware('auth.jwt');
    Route::post('store', 'store');
});

Route::get('agent-applications', [ApplicationController::class, 'getAllApplicationsByAgent'])->middleware('auth.jwt');
Route::get('applications', [ApplicationController::class, 'getAllApplications'])->middleware('auth.jwt');
Route::get('statistics', [ApplicationController::class, 'getStatistics'])->middleware('auth.jwt');
Route::get('application/preload-data', [ApplicationController::class, 'preloadData'])->middleware('auth.jwt');
Route::get('notifications', [NotificationController::class, 'index'])->middleware('auth.jwt');
Route::post('notifications/{notification}/read', [NotificationController::class, 'update'])->middleware('auth.jwt');

Route::group(['prefix' => 'admin', 'middleware' => 'admin.portal'], function () {
    Route::get('preload-data', [ApplicationController::class, 'preloadData']);
    Route::group(['prefix' => 'application'], function () {
        Route::get('data-table', [ApplicationController::class, 'dataTable']);
        Route::group(['prefix' => '{id}'], function () {
            Route::get('/', [ApplicationController::class, 'show']);
            Route::post('status-update', [ApplicationController::class, 'updateStatus']);
        });
    });
});

Route::get('configurations', [ConfigurationController::class, 'index']);
Route::get('configurations/{config}', [ConfigurationController::class, 'show']);
Route::patch('configurations/update', [ConfigurationController::class, 'update']);

Route::patch('application/{application}/resubmit/request', [ApplicationResubmissionController::class, 'resubmitRequest']);

Route::middleware(['auth.jwt'])->group(function () {
    Route::resource('events', EventController::class)->only(['index', 'store', 'update', 'destroy']);
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

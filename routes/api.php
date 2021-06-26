<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MethodController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SummaryController;
use App\Http\Controllers\Api\TypeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

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
Route::group([
    'middleware' => 'api',
], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        //Auth
        Route::post('/login', [AuthController::class, 'login']);
        Route::group([
            'middleware' => 'auth:api',
        ], function () {
            Route::get('/user', [AuthController::class, 'user']);
            Route::patch('/password', [AuthController::class, 'changePassword']);
        });
    });
    //Auth API
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        Route::get('/jobs/additions', [JobController::class, 'additions']);
        Route::get('/jobs/rate', [JobController::class, 'getRate']);
        Route::resource('customers', CustomerController::class);
        Route::resource('methods', MethodController::class);
        Route::resource('types', TypeController::class);
        Route::resource('jobs', JobController::class);
        //Summary
        Route::get('/report-chart', [SummaryController::class, 'chartReport']);
        //Report
        Route::get('/report', [ReportController::class, 'index']);
    });
});


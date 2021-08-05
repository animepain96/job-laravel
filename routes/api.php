<?php

use Illuminate\Http\Request;

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
        Route::post('/login', 'Api\AuthController@login');
        Route::group([
            'middleware' => 'auth:api',
        ], function () {
            Route::get('/user', 'Api\AuthController@user');
            Route::patch('/password', 'Api\AuthController@changePassword');
        });
    });
    //Auth API
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        Route::get('/jobs/additions', 'Api\JobController@additions');
        Route::get('/jobs/monthly-revenue', 'Api\JobController@monthlyRevenue');
        Route::get('/jobs/rate', 'Api\JobController@getRate');
        Route::resource('customers', 'Api\CustomerController');
        Route::resource('methods', 'Api\MethodController');
        Route::resource('types', 'Api\TypeController');
        Route::resource('jobs', 'Api\JobController');
        //Summary
        Route::get('/report-chart', 'Api\SummaryController@chartReport');
        Route::get('/unpaid-count', 'Api\SummaryController@unpaidCount');
        //Setting
        Route::get('/unpaid-threshold', 'Api\SettingController@unpaidThreshold');
        Route::patch('/unpaid-threshold', 'Api\SettingController@updateUnpaidThreshold');
        Route::get('/keep-days', 'Api\SettingController@keepDays');
        Route::patch('/keep-days', 'Api\SettingController@updateKeepDays')->middleware('super.admin');
        //Report
        Route::get('/report', 'Api\ReportController@index');
        Route::get('/report/revenue', 'Api\ReportController@totalRevenue');
        //User
        Route::group([
            'prefix' => 'users',
            'middleware' => 'admin',
        ], function () {
            Route::get('/', 'Api\UserController@index');
            Route::patch('/{id}', 'Api\UserController@update')->middleware('super.admin');
            Route::post('/', 'Api\UserController@store');
            Route::delete('/{id}', 'Api\UserController@destroy')->middleware('super.admin');
        });
        Route::post('/users/{id}/password', 'Api\UserController@resetPassword');
        //Backup
        Route::group([
            'prefix' => 'backups',
            'middleware' => 'admin',
        ], function () {
            Route::get('/manual', 'Api\BackupController@manual');
            Route::get('/', 'Api\BackupController@index');
            Route::post('/delete', 'Api\BackupController@deleteBackup')->middleware('super.admin');
            Route::get('/download', 'Api\BackupController@download');
        });
    });
});

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('login/staff', 'StaffController@login');
    Route::post('signup', 'AuthController@signup');
    Route::get('signup/activate/{token}', 'AuthController@signupActivate');
  
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'job'
], function () {
    Route::post('create', 'JobController@store');
    Route::post('edit/{id}', 'JobController@update');
    Route::post('status/{id}', 'JobController@estado');
    Route::get('generate/{id}', 'JobController@generate');
    Route::get('delete/{id}', 'JobController@destroy');
    Route::get('show/{id}', 'JobController@show');
    Route::get('confirm/{id}', 'JobController@confirm');
    Route::get('list', 'JobController@index');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'staff'
], function () {
    Route::post('create', 'StaffController@store');
    Route::post('check', 'StaffController@check_email');
    Route::post('edit/{id}', 'StaffController@update');
    Route::get('delete/{id}', 'StaffController@destroy');
    Route::get('show/{id}', 'StaffController@show');
    Route::get('list', 'StaffController@index');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'client'
], function () {
    Route::post('create', 'ClientController@store');
    Route::post('edit/{id}', 'ClientController@update');
    Route::get('delete/{id}', 'ClientController@destroy');
    Route::get('show/{id}', 'ClientController@show');
    Route::get('list', 'ClientController@index');
});


Route::group([    
    'namespace' => 'Auth',    
    'middleware' => 'api',    
    'prefix' => 'password'
], function () {    
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});
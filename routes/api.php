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
    Route::post('edit', 'JobController@update');
    Route::get('show/{token}', 'JobController@show');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'staff'
], function () {
    Route::post('create', 'StaffController@store');
    Route::post('edit', 'StaffController@update');
    Route::get('show/{token}', 'StaffController@show');
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
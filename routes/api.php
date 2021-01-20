<?php

use Illuminate\Http\Request;
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

Route::post('doctors' , 'App\Http\Controllers\DoctorController@store');
Route::post('login', 'App\Http\Controllers\AuthController@login');

Route::group(['middleware' => ['auth:api']], function () {

Route::group(['middleware' => ['can:do_everything']], function () {

    Route::post('logout', 'App\Http\Controllers\AuthController@logout');

    Route::get('doctors' , 'App\Http\Controllers\DoctorController@index');
    Route::get('doctors/{id}' , 'App\Http\Controllers\DoctorController@show');
    Route::post('doctors/{id}' , 'App\Http\Controllers\DoctorController@edit');
    Route::delete('doctors/{id}' , 'App\Http\Controllers\DoctorController@destroy');

    Route::post('patients' , 'App\Http\Controllers\PatientController@store');
    Route::get('patients' , 'App\Http\Controllers\PatientController@index');
    Route::get('patients/{id}' , 'App\Http\Controllers\PatientController@show');
    Route::post('patients/{id}' , 'App\Http\Controllers\PatientController@edit');
    Route::delete('patients/{id}' , 'App\Http\Controllers\PatientController@destroy');

    Route::post('news' , 'App\Http\Controllers\NewsController@store');
    Route::get('news' , 'App\Http\Controllers\NewsController@index');
    Route::get('news/{id}' , 'App\Http\Controllers\NewsController@show');
    Route::post('news/{id}' , 'App\Http\Controllers\NewsController@edit');
    Route::delete('news/{id}' , 'App\Http\Controllers\NewsController@destroy');
    
});
});
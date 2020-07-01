<?php

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

Route::get('/user', 'HomeController@user')->name('userData')->middleware('auth:api');

Route::group(['prefix' => 'cloud', 'middleware' => ['auth:api']], function () {
    Route::get('pdfs', 'CloudController@pdfs');
    Route::get('codes/{code}','CloudController@code');
    Route::post('pdfs', 'CloudController@store');
});

<?php

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

Route::get( '/', 'MainController@mainhome' );

Route::get( '/loaderio-9c45e264530f0331a41f35745ceb669f', 'MainController@loader' );

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

Route::get( '/loaderio-76bcf8aa092bbd43c6f55d9ede614b47', 'MainController@loader' );

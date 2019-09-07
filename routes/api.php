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

Route::post('login', 'UserController@login');
Route::get('home', 'MainController@home');
Route::get('food', 'MainController@food');
Route::get('snacks', 'MainController@snacks');
Route::get('bread', 'MainController@bread');
Route::get('dinner', 'MainController@dinner');
Route::get('launch', 'MainController@launch');
Route::get('breakfast', 'MainController@breakfast');
Route::get('chefs', 'MainController@chefs');
Route::get('chef', 'MainController@chef');
Route::get('restaurants', 'MainController@restaurants');
Route::get('restaurants', 'MainController@restaurants');
Route::post('order', 'MainController@order');
Route::group(['middleware' => 'auth:api'], function()
{
   Route::post('details', 'UserController@details');
});

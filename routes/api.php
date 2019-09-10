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
Route::post('register', 'UserController@register');
Route::get('home', 'MainController@home');
Route::get('food', 'MainController@food');
Route::get('latestfood', 'MainController@latestfood');
Route::get('snacks', 'MainController@snacks');
Route::get('snack', 'MainController@snack');
Route::get('breads', 'MainController@breads');
Route::get('bread', 'MainController@bread');
Route::get('dinners', 'MainController@dinners');
Route::get('dinner', 'MainController@dinner');
Route::get('launch', 'MainController@launch');
Route::get('breakfast', 'MainController@breakfast');
Route::get('chefs', 'MainController@chefs');
Route::prefix('vendor')->group(function () {
   Route::post('save', 'VendorController@save')->middleware('auth:api');
   Route::post('update', 'VendorController@update')->middleware('auth:api');
   Route::post('find', 'VendorController@find');
   Route::post('delete', 'VendorController@delete')->middleware('auth:api');
   Route::post('all', 'VendorController@all');
});
Route::prefix('review')->group(function () {
   Route::post('save', 'ReviewsController@save')->middleware('auth:api');
   Route::post('update', 'ReviewsController@update')->middleware('auth:api');
   Route::post('find', 'ReviewsController@find');
   Route::post('delete', 'ReviewsController@delete');
   Route::post('all', 'ReviewsController@all');
});
Route::prefix('food')->group(function () {
   Route::post('save', 'FoodController@save');
   Route::post('update', 'FoodController@update');
   Route::post('find', 'FoodController@find');
   Route::post('delete', 'FoodController@delete');
   Route::post('all', 'FoodController@all');
});
Route::get('chef', 'MainController@chef');
Route::get('restaurants', 'MainController@restaurants');
Route::get('restaurant', 'MainController@restaurant');
Route::post('order', 'MainController@order');
Route::group(['middleware' => 'auth:api'], function()
{
   Route::post('details', 'UserController@details');
});
